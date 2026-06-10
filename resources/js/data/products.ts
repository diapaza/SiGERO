import type { Product } from '@/types'

export const recentProducts: Product[] = [
  {
    id: 1,
    name: 'Macbook pro 13"',
    variants: 2,
    image: '/images/product/product-01.jpg',
    category: 'Laptop',
    price: '$2399.00',
    status: 'Delivered',
  },
  {
    id: 2,
    name: 'Apple Watch Ultra',
    variants: 1,
    image: '/images/product/product-02.jpg',
    category: 'Watch',
    price: '$879.00',
    status: 'Pending',
  },
  {
    id: 3,
    name: 'iPhone 15 Pro Max',
    variants: 2,
    image: '/images/product/product-03.jpg',
    category: 'SmartPhone',
    price: '$1869.00',
    status: 'Delivered',
  },
  {
    id: 4,
    name: 'iPad Pro 3rd Gen',
    variants: 2,
    image: '/images/product/product-04.jpg',
    category: 'Electronics',
    price: '$1699.00',
    status: 'Canceled',
  },
  {
    id: 5,
    name: 'Airpods Pro 2nd Gen',
    variants: 1,
    image: '/images/product/product-05.jpg',
    category: 'Accessories',
    price: '$240.00',
    status: 'Delivered',
  },
]
