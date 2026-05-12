-- ============================================================
-- 002_rls_policies.sql
-- Row Level Security — all access control via Supabase Auth
--
-- Run AFTER 001_initial_schema.sql
-- ============================================================

-- ──────────────────────────────────────────────────────────
-- Helper: get the role of the currently authenticated user
-- ──────────────────────────────────────────────────────────
CREATE OR REPLACE FUNCTION public.auth_user_role()
RETURNS TEXT LANGUAGE sql STABLE SECURITY DEFINER AS $$
  SELECT role FROM public.users WHERE id = auth.uid()
$$;

-- ──────────────────────────────────────────────────────────
-- Enable RLS on all tables
-- ──────────────────────────────────────────────────────────
ALTER TABLE public.users          ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.expeditions    ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.leads          ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.lead_notes     ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.checklist_items ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.media          ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.bookings       ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.payments       ENABLE ROW LEVEL SECURITY;

-- ──────────────────────────────────────────────────────────
-- USERS policies
-- ──────────────────────────────────────────────────────────
-- Any authenticated user can view their own profile
CREATE POLICY "users_select_own"
  ON public.users FOR SELECT
  USING (id = auth.uid());

-- Admins can view all users
CREATE POLICY "users_select_admin"
  ON public.users FOR SELECT
  USING (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN'));

-- Users can update their own profile
CREATE POLICY "users_update_own"
  ON public.users FOR UPDATE
  USING (id = auth.uid())
  WITH CHECK (id = auth.uid());

-- Only MASTER_ADMIN can insert/update/delete other users
CREATE POLICY "users_manage_master"
  ON public.users FOR ALL
  USING (public.auth_user_role() = 'MASTER_ADMIN');

-- ──────────────────────────────────────────────────────────
-- EXPEDITIONS policies
-- ──────────────────────────────────────────────────────────
-- All authenticated users can view non-deleted expeditions
CREATE POLICY "expeditions_select"
  ON public.expeditions FOR SELECT
  USING (auth.uid() IS NOT NULL AND deleted_at IS NULL);

-- Published expeditions are public (for traveler portal)
CREATE POLICY "expeditions_select_public"
  ON public.expeditions FOR SELECT
  USING (status = 'published' AND deleted_at IS NULL);

-- ADMIN, MASTER_ADMIN, OPERATOR can create expeditions
CREATE POLICY "expeditions_insert"
  ON public.expeditions FOR INSERT
  WITH CHECK (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR'));

-- ADMIN, MASTER_ADMIN, OPERATOR can update expeditions
CREATE POLICY "expeditions_update"
  ON public.expeditions FOR UPDATE
  USING (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR'));

-- Only ADMIN/MASTER_ADMIN can delete (soft-delete)
CREATE POLICY "expeditions_delete"
  ON public.expeditions FOR DELETE
  USING (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN'));

-- ──────────────────────────────────────────────────────────
-- LEADS policies
-- ──────────────────────────────────────────────────────────
-- Authenticated staff can view all leads
CREATE POLICY "leads_select"
  ON public.leads FOR SELECT
  USING (
    auth.uid() IS NOT NULL
    AND public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR')
  );

-- Guides see only leads assigned to them
CREATE POLICY "leads_select_guide"
  ON public.leads FOR SELECT
  USING (
    public.auth_user_role() = 'GUIDE'
    AND assigned_to = auth.uid()
  );

CREATE POLICY "leads_insert"
  ON public.leads FOR INSERT
  WITH CHECK (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR'));

CREATE POLICY "leads_update"
  ON public.leads FOR UPDATE
  USING (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR'));

CREATE POLICY "leads_delete"
  ON public.leads FOR DELETE
  USING (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN'));

-- ──────────────────────────────────────────────────────────
-- LEAD NOTES policies
-- ──────────────────────────────────────────────────────────
CREATE POLICY "lead_notes_select"
  ON public.lead_notes FOR SELECT
  USING (
    auth.uid() IS NOT NULL
    AND public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR','GUIDE')
  );

CREATE POLICY "lead_notes_insert"
  ON public.lead_notes FOR INSERT
  WITH CHECK (
    auth.uid() IS NOT NULL
    AND public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR','GUIDE')
  );

-- Authors can update/delete their own notes; admins can manage all
CREATE POLICY "lead_notes_update"
  ON public.lead_notes FOR UPDATE
  USING (
    author_id = auth.uid()
    OR public.auth_user_role() IN ('MASTER_ADMIN','ADMIN')
  );

CREATE POLICY "lead_notes_delete"
  ON public.lead_notes FOR DELETE
  USING (
    author_id = auth.uid()
    OR public.auth_user_role() IN ('MASTER_ADMIN','ADMIN')
  );

-- ──────────────────────────────────────────────────────────
-- CHECKLIST ITEMS policies
-- ──────────────────────────────────────────────────────────
CREATE POLICY "checklist_select"
  ON public.checklist_items FOR SELECT
  USING (auth.uid() IS NOT NULL);

CREATE POLICY "checklist_insert"
  ON public.checklist_items FOR INSERT
  WITH CHECK (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR'));

CREATE POLICY "checklist_update"
  ON public.checklist_items FOR UPDATE
  USING (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR','GUIDE'));

CREATE POLICY "checklist_delete"
  ON public.checklist_items FOR DELETE
  USING (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR'));

-- ──────────────────────────────────────────────────────────
-- MEDIA policies
-- ──────────────────────────────────────────────────────────
CREATE POLICY "media_select"
  ON public.media FOR SELECT
  USING (auth.uid() IS NOT NULL);

CREATE POLICY "media_insert"
  ON public.media FOR INSERT
  WITH CHECK (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR','GUIDE'));

CREATE POLICY "media_delete"
  ON public.media FOR DELETE
  USING (
    uploaded_by = auth.uid()
    OR public.auth_user_role() IN ('MASTER_ADMIN','ADMIN')
  );

-- ──────────────────────────────────────────────────────────
-- BOOKINGS policies
-- ──────────────────────────────────────────────────────────
CREATE POLICY "bookings_select_staff"
  ON public.bookings FOR SELECT
  USING (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR'));

-- Travelers see only their own bookings
CREATE POLICY "bookings_select_own"
  ON public.bookings FOR SELECT
  USING (traveler_id = auth.uid());

CREATE POLICY "bookings_insert"
  ON public.bookings FOR INSERT
  WITH CHECK (
    public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR')
    OR traveler_id = auth.uid()
  );

CREATE POLICY "bookings_update"
  ON public.bookings FOR UPDATE
  USING (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR'));

CREATE POLICY "bookings_delete"
  ON public.bookings FOR DELETE
  USING (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN'));

-- ──────────────────────────────────────────────────────────
-- PAYMENTS policies
-- ──────────────────────────────────────────────────────────
CREATE POLICY "payments_select"
  ON public.payments FOR SELECT
  USING (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR'));

CREATE POLICY "payments_insert"
  ON public.payments FOR INSERT
  WITH CHECK (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN','OPERATOR'));

CREATE POLICY "payments_update"
  ON public.payments FOR UPDATE
  USING (public.auth_user_role() IN ('MASTER_ADMIN','ADMIN'));

-- ──────────────────────────────────────────────────────────
-- STORAGE — Bucket policies
-- ──────────────────────────────────────────────────────────
-- Run these in the Supabase Dashboard → Storage after creating buckets:
--   • expeditions-media   (public)
--   • avatars             (public)
--   • documents           (private)

-- Allow authenticated users to upload to expeditions-media
-- INSERT INTO storage.policies (bucket_id, name, definition) VALUES (
--   'expeditions-media',
--   'Authenticated users can upload',
--   'auth.uid() IS NOT NULL'
-- );
-- (Use the Supabase Dashboard UI to set storage policies — SQL above is illustrative)
