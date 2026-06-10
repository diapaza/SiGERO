export interface User {
  id: number
  name: string
  email: string
  role: string
  avatar: string
  status: 'Active' | 'Pending' | 'Cancel' | 'online' | 'offline'
  phone?: string
  bio?: string
  socialLinks?: SocialLinks
}

export interface SocialLinks {
  facebook?: string
  twitter?: string
  linkedin?: string
  instagram?: string
}

export interface Product {
  id: number
  name: string
  variants: number
  image: string
  category: string
  price: string
  status: 'Delivered' | 'Pending' | 'Canceled'
}

export interface Notification {
  id: number
  userName: string
  userImage: string
  action: string
  project: string
  type: string
  time: string
  status: 'online' | 'offline'
}

export interface TeamMember {
  name: string
  role: string
  avatar: string
  project: string
  team: string[]
  status: 'Active' | 'Pending' | 'Cancel'
  budget: string
}

export interface CalendarEvent {
  id: number
  title: string
  start: Date
  end?: Date
  color: string
  description?: string
}

export interface MenuItem {
  name: string
  path?: string
  icon?: React.ComponentType
  new?: boolean
  pro?: boolean
}

export interface MenuGroup {
  title: string
  items: Array<{
    icon?: any
    name: string
    path?: string
    subItems?: Array<{
      name: string
      path: string
      new?: boolean
      pro?: boolean
    }>
  }>
}
