'use client'

import { useEffect, useState, useRef } from 'react'
import { createClient } from '@/utils/supabase/client'

interface MediaItem {
  name: string
  id: string
  created_at: string | null
  updated_at: string | null
  metadata: { size: number; mimetype: string } | null
  publicUrl: string
}

const BUCKET = 'expeditions-media'

function formatBytes(bytes: number): string {
  if (bytes < 1024) return `${bytes} B`
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}

function isImage(mime: string) { return mime?.startsWith('image/') }
function isVideo(mime: string) { return mime?.startsWith('video/') }

export default function MediaPage() {
  const supabase = createClient()
  const [items, setItems] = useState<MediaItem[]>([])
  const [loading, setLoading] = useState(true)
  const [uploading, setUploading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const fileRef = useRef<HTMLInputElement>(null)

  async function loadMedia() {
    setLoading(true)
    const { data, error } = await supabase.storage.from(BUCKET).list('', {
      limit: 100, sortBy: { column: 'created_at', order: 'desc' },
    })
    if (error) { setError(error.message); setLoading(false); return }

    const enriched: MediaItem[] = (data ?? []).map((f) => {
      const { data: { publicUrl } } = supabase.storage.from(BUCKET).getPublicUrl(f.name)
      return { ...f, id: f.id ?? f.name, publicUrl, metadata: f.metadata as MediaItem['metadata'] }
    })
    setItems(enriched)
    setLoading(false)
  }

  useEffect(() => { loadMedia() }, [])

  async function handleUpload(e: React.ChangeEvent<HTMLInputElement>) {
    const files = Array.from(e.target.files ?? [])
    if (!files.length) return
    setUploading(true)
    setError(null)
    for (const file of files) {
      const path = `${Date.now()}_${file.name}`
      const { error } = await supabase.storage.from(BUCKET).upload(path, file)
      if (error) { setError(error.message) }
    }
    setUploading(false)
    if (fileRef.current) fileRef.current.value = ''
    await loadMedia()
  }

  async function handleDelete(name: string) {
    const { error } = await supabase.storage.from(BUCKET).remove([name])
    if (error) { setError(error.message); return }
    setItems(prev => prev.filter(i => i.name !== name))
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Banco de Mídias</h1>
          <p className="mt-1 text-sm text-gray-500">Gerencie fotos e vídeos das expedições</p>
        </div>
        <label className={`px-4 py-2 rounded-lg text-sm font-medium cursor-pointer transition-colors ${uploading ? 'bg-gray-200 text-gray-500 cursor-not-allowed' : 'bg-brand-600 hover:bg-brand-700 text-white'}`}>
          {uploading ? 'Enviando…' : '+ Upload'}
          <input
            ref={fileRef}
            type="file"
            multiple
            accept="image/*,video/*"
            className="hidden"
            disabled={uploading}
            onChange={handleUpload}
          />
        </label>
      </div>

      {error && (
        <div className="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3">
          {error}
        </div>
      )}

      {loading ? (
        <div className="py-20 flex justify-center items-center text-gray-400 text-sm">Carregando mídias…</div>
      ) : items.length === 0 ? (
        <div className="py-24 flex flex-col items-center gap-3 text-gray-400">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.2" opacity=".4">
            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
          </svg>
          <p className="text-sm">Nenhuma mídia ainda. Faça upload de fotos ou vídeos.</p>
        </div>
      ) : (
        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
          {items.map((item) => {
            const mime = item.metadata?.mimetype ?? ''
            const size = item.metadata?.size ?? 0
            return (
              <div key={item.id ?? item.name} className="group relative bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                {/* Thumbnail */}
                <div className="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
                  {isImage(mime) ? (
                    // eslint-disable-next-line @next/next/no-img-element
                    <img src={item.publicUrl} alt={item.name} className="w-full h-full object-cover" />
                  ) : isVideo(mime) ? (
                    <video src={item.publicUrl} className="w-full h-full object-cover" muted />
                  ) : (
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" className="text-gray-400">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                      <polyline points="14 2 14 8 20 8"/>
                    </svg>
                  )}
                </div>

                {/* Delete overlay */}
                <button
                  onClick={() => handleDelete(item.name)}
                  className="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-red-500 text-white text-xs items-center justify-center hidden group-hover:flex transition-all hover:bg-red-600"
                  aria-label="Remover"
                >
                  ×
                </button>

                {/* Info */}
                <div className="p-2">
                  <p className="text-xs text-gray-700 font-medium truncate" title={item.name}>{item.name}</p>
                  <p className="text-[10px] text-gray-400">{formatBytes(size)}</p>
                </div>
              </div>
            )
          })}
        </div>
      )}
    </div>
  )
}
