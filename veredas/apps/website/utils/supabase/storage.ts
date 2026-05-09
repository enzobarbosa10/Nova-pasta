// ============================================================
// storage.ts — Supabase Storage helpers (client-side safe)
// ============================================================
import { createClient } from './client'

export type StorageBucket = 'avatars' | 'expeditions' | 'agency-media' | 'documents'

export interface UploadResult {
  url: string | null
  path: string | null
  error: string | null
}

// ── Upload ─────────────────────────────────────────────────

export async function uploadFile(
  bucket: StorageBucket,
  path: string,
  file: File,
  options?: { upsert?: boolean; contentType?: string },
): Promise<UploadResult> {
  const supabase = createClient()
  const { data, error } = await supabase.storage.from(bucket).upload(path, file, {
    upsert: options?.upsert ?? true,
    contentType: options?.contentType ?? file.type,
  })

  if (error) {
    console.error(`[storage] upload(${bucket}/${path}):`, error.message)
    return { url: null, path: null, error: error.message }
  }

  const { data: urlData } = supabase.storage.from(bucket).getPublicUrl(data.path)
  return { url: urlData.publicUrl, path: data.path, error: null }
}

export async function uploadAvatar(userId: string, file: File): Promise<UploadResult> {
  const ext = file.name.split('.').pop() ?? 'jpg'
  return uploadFile('avatars', `${userId}/avatar.${ext}`, file, { upsert: true })
}

export async function uploadExpeditionImage(
  expeditionId: string,
  file: File,
): Promise<UploadResult> {
  const ext = file.name.split('.').pop() ?? 'jpg'
  const filename = `${Date.now()}.${ext}`
  return uploadFile('expeditions', `${expeditionId}/${filename}`, file)
}

export async function uploadAgencyMedia(
  agencyId: string,
  file: File,
): Promise<UploadResult> {
  const ext = file.name.split('.').pop() ?? 'jpg'
  const filename = `${Date.now()}.${ext}`
  return uploadFile('agency-media', `${agencyId}/${filename}`, file)
}

// ── Delete ─────────────────────────────────────────────────

export async function deleteFile(
  bucket: StorageBucket,
  path: string,
): Promise<{ error: string | null }> {
  const supabase = createClient()
  const { error } = await supabase.storage.from(bucket).remove([path])
  if (error) {
    console.error(`[storage] delete(${bucket}/${path}):`, error.message)
    return { error: error.message }
  }
  return { error: null }
}

// ── Public URL ─────────────────────────────────────────────

export function getPublicUrl(bucket: StorageBucket, path: string): string {
  const supabase = createClient()
  const { data } = supabase.storage.from(bucket).getPublicUrl(path)
  return data.publicUrl
}

// ── List files ─────────────────────────────────────────────

export async function listFiles(
  bucket: StorageBucket,
  folder: string,
): Promise<{ name: string; url: string }[]> {
  const supabase = createClient()
  const { data, error } = await supabase.storage.from(bucket).list(folder)
  if (error || !data) return []

  return data.map((file) => ({
    name: file.name,
    url: getPublicUrl(bucket, `${folder}/${file.name}`),
  }))
}
