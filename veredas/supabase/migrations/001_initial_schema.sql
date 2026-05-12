-- ============================================================
-- 001_initial_schema.sql
-- Complete Supabase PostgreSQL schema — migrated from Laravel/MySQL
--
-- Run this in the Supabase SQL Editor:
--   Dashboard → SQL Editor → New Query → paste → Run
-- ============================================================

-- Enable required extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pg_trgm";   -- trigram search on leads

-- ──────────────────────────────────────────────────────────
-- USERS (mirrors auth.users — public profile table)
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS public.users (
  id            UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
  email         TEXT NOT NULL UNIQUE,
  full_name     TEXT,
  avatar_url    TEXT,
  role          TEXT NOT NULL DEFAULT 'OPERATOR'
                  CHECK (role IN ('MASTER_ADMIN','ADMIN','OPERATOR','GUIDE','TRAVELER')),
  is_active     BOOLEAN NOT NULL DEFAULT TRUE,
  last_login_at TIMESTAMPTZ,
  created_at    TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at    TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Auto-populate users row when a new auth user is created
CREATE OR REPLACE FUNCTION public.handle_new_user()
RETURNS TRIGGER LANGUAGE plpgsql SECURITY DEFINER SET search_path = public AS $$
BEGIN
  INSERT INTO public.users (id, email, full_name, avatar_url)
  VALUES (
    NEW.id,
    NEW.email,
    COALESCE(NEW.raw_user_meta_data->>'full_name', split_part(NEW.email, '@', 1)),
    NEW.raw_user_meta_data->>'avatar_url'
  )
  ON CONFLICT (id) DO NOTHING;
  RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS on_auth_user_created ON auth.users;
CREATE TRIGGER on_auth_user_created
  AFTER INSERT ON auth.users
  FOR EACH ROW EXECUTE PROCEDURE public.handle_new_user();

-- ──────────────────────────────────────────────────────────
-- EXPEDITIONS
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS public.expeditions (
  id                UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  title             TEXT NOT NULL,
  slug              TEXT UNIQUE,
  description       TEXT,
  destination       TEXT NOT NULL,
  start_date        DATE NOT NULL,
  end_date          DATE NOT NULL CHECK (end_date >= start_date),
  max_travelers     INTEGER NOT NULL CHECK (max_travelers > 0),
  current_travelers INTEGER NOT NULL DEFAULT 0 CHECK (current_travelers >= 0),
  price_per_person  NUMERIC(12,2) NOT NULL DEFAULT 0,
  currency          TEXT NOT NULL DEFAULT 'BRL',
  cover_image_url   TEXT,
  guide_id          UUID REFERENCES public.users(id) ON DELETE SET NULL,
  accommodation     TEXT,
  transport         TEXT,
  trail_level       TEXT CHECK (trail_level IN ('EASY','MODERATE','HARD','CHALLENGING')),
  costs             NUMERIC(12,2) DEFAULT 0,
  margin_predicted  NUMERIC(5,2)  DEFAULT 0,
  margin_real       NUMERIC(5,2),
  participants      JSONB DEFAULT '[]',
  status            TEXT NOT NULL DEFAULT 'draft'
                      CHECK (status IN ('draft','published','ongoing','completed','cancelled')),
  deleted_at        TIMESTAMPTZ,           -- soft delete
  created_at        TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at        TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS expeditions_status_idx    ON public.expeditions(status);
CREATE INDEX IF NOT EXISTS expeditions_start_date_idx ON public.expeditions(start_date);
CREATE INDEX IF NOT EXISTS expeditions_deleted_at_idx ON public.expeditions(deleted_at);

-- ──────────────────────────────────────────────────────────
-- LEADS (CRM)
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS public.leads (
  id               UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  name             TEXT NOT NULL,
  email            TEXT,
  phone            TEXT,           -- whatsapp
  instagram        TEXT,
  source           TEXT,
  interest         TEXT,
  destination      TEXT,
  date_desired     DATE,
  people_count     INTEGER DEFAULT 1,
  total_price      NUMERIC(12,2),  -- estimated_ticket
  status           TEXT NOT NULL DEFAULT 'NEW'
                     CHECK (status IN ('NEW','CONTACTED','QUALIFIED','PROPOSAL','RESERVED','PAID','POST_TRIP','REFERRAL')),
  notes            TEXT,
  last_contact     DATE,
  next_follow_up   DATE,
  tags             JSONB DEFAULT '[]',
  expedition_id    UUID REFERENCES public.expeditions(id) ON DELETE SET NULL,
  assigned_to      UUID REFERENCES public.users(id) ON DELETE SET NULL,
  created_at       TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at       TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Trigram GIN index for fast full-text search
CREATE INDEX IF NOT EXISTS leads_name_trgm_idx  ON public.leads USING GIN (name  gin_trgm_ops);
CREATE INDEX IF NOT EXISTS leads_email_trgm_idx ON public.leads USING GIN (email gin_trgm_ops);
CREATE INDEX IF NOT EXISTS leads_status_idx     ON public.leads(status);
CREATE INDEX IF NOT EXISTS leads_next_follow_up ON public.leads(next_follow_up);

-- ──────────────────────────────────────────────────────────
-- LEAD NOTES
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS public.lead_notes (
  id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  lead_id    UUID NOT NULL REFERENCES public.leads(id) ON DELETE CASCADE,
  author_id  UUID REFERENCES public.users(id) ON DELETE SET NULL,
  content    TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS lead_notes_lead_id_idx ON public.lead_notes(lead_id);

-- ──────────────────────────────────────────────────────────
-- CHECKLIST ITEMS (per expedition)
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS public.checklist_items (
  id            UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  expedition_id UUID NOT NULL REFERENCES public.expeditions(id) ON DELETE CASCADE,
  label         TEXT NOT NULL,
  is_done       BOOLEAN NOT NULL DEFAULT FALSE,
  due_date      DATE,
  assigned_to   UUID REFERENCES public.users(id) ON DELETE SET NULL,
  sort_order    INTEGER DEFAULT 0,
  created_at    TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at    TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS checklist_expedition_idx ON public.checklist_items(expedition_id);

-- ──────────────────────────────────────────────────────────
-- MEDIA (linked to expeditions)
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS public.media (
  id            UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  expedition_id UUID REFERENCES public.expeditions(id) ON DELETE CASCADE,
  uploaded_by   UUID REFERENCES public.users(id) ON DELETE SET NULL,
  url           TEXT NOT NULL,
  storage_path  TEXT NOT NULL,
  type          TEXT NOT NULL CHECK (type IN ('image','video','document')),
  name          TEXT NOT NULL,
  size_bytes    BIGINT DEFAULT 0,
  mime_type     TEXT,
  created_at    TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS media_expedition_idx ON public.media(expedition_id);

-- ──────────────────────────────────────────────────────────
-- BOOKINGS
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS public.bookings (
  id            UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  expedition_id UUID NOT NULL REFERENCES public.expeditions(id) ON DELETE RESTRICT,
  traveler_id   UUID NOT NULL REFERENCES public.users(id) ON DELETE RESTRICT,
  lead_id       UUID REFERENCES public.leads(id) ON DELETE SET NULL,
  seats         INTEGER NOT NULL DEFAULT 1 CHECK (seats > 0),
  total_price   NUMERIC(12,2) NOT NULL,
  currency      TEXT NOT NULL DEFAULT 'BRL',
  status        TEXT NOT NULL DEFAULT 'pending'
                  CHECK (status IN ('pending','confirmed','cancelled','completed')),
  notes         TEXT,
  created_at    TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at    TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS bookings_expedition_idx ON public.bookings(expedition_id);
CREATE INDEX IF NOT EXISTS bookings_traveler_idx   ON public.bookings(traveler_id);
CREATE INDEX IF NOT EXISTS bookings_status_idx     ON public.bookings(status);

-- ──────────────────────────────────────────────────────────
-- PAYMENTS
-- ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS public.payments (
  id          UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
  booking_id  UUID NOT NULL REFERENCES public.bookings(id) ON DELETE RESTRICT,
  amount      NUMERIC(12,2) NOT NULL,
  currency    TEXT NOT NULL DEFAULT 'BRL',
  status      TEXT NOT NULL DEFAULT 'pending'
                CHECK (status IN ('pending','paid','refunded','failed')),
  method      TEXT CHECK (method IN ('credit_card','pix','bank_transfer')),
  gateway_id  TEXT,
  paid_at     TIMESTAMPTZ,
  created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW(),
  updated_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS payments_booking_idx ON public.payments(booking_id);
CREATE INDEX IF NOT EXISTS payments_status_idx  ON public.payments(status);

-- ──────────────────────────────────────────────────────────
-- updated_at trigger helper
-- ──────────────────────────────────────────────────────────
CREATE OR REPLACE FUNCTION public.set_updated_at()
RETURNS TRIGGER LANGUAGE plpgsql AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$;

DO $$
DECLARE
  t TEXT;
BEGIN
  FOREACH t IN ARRAY ARRAY[
    'users','expeditions','leads','lead_notes',
    'checklist_items','bookings','payments'
  ] LOOP
    EXECUTE format(
      'DROP TRIGGER IF EXISTS set_%I_updated_at ON public.%I;
       CREATE TRIGGER set_%I_updated_at
         BEFORE UPDATE ON public.%I
         FOR EACH ROW EXECUTE PROCEDURE public.set_updated_at();',
      t, t, t, t
    );
  END LOOP;
END;
$$;
