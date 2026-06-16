const gradients = [
  { from: '#FF6B6B', to: '#EE5A24' },
  { from: '#48DBFB', to: '#0ABDE3' },
  { from: '#F368E0', to: '#BE2EDD' },
  { from: '#54A0FF', to: '#2E86DE' },
  { from: '#5F27CD', to: '#341F97' },
  { from: '#00D2D3', to: '#01A3A4' },
  { from: '#FECA57', to: '#F0932B' },
  { from: '#FF6348', to: '#EB3B5A' },
  { from: '#7BED9F', to: '#2ED573' },
  { from: '#A29BFE', to: '#6C5CE7' },
  { from: '#FD79A8', to: '#E84393' },
  { from: '#00CEC9', to: '#00B894' },
  { from: '#E17055', to: '#D63031' },
  { from: '#0984E3', to: '#074B8C' },
  { from: '#F8A5C2', to: '#F78FB3' },
  { from: '#63CDDA', to: '#3DC1D3' },
]

export function getInitials(name: string): string {
  if (!name || name.trim() === '') return '?'
  const normalized = name.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
  const parts = normalized.trim().split(/\s+/)
  if (parts.length === 1) return parts[0].charAt(0).toUpperCase()
  return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase()
}

export function getGradientColors(name: string): { from: string; to: string } {
  if (!name || name.trim() === '') return gradients[0]
  const initials = getInitials(name)
  let hash = 0
  for (let i = 0; i < initials.length; i++) {
    hash = initials.charCodeAt(i) + ((hash << 5) - hash)
  }
  return gradients[Math.abs(hash) % gradients.length]
}
