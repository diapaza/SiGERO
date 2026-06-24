import type { useForm } from '@inertiajs/vue3'

interface ValidationRule {
  required?: boolean
  maxLength?: number
  minLength?: number
  size?: number
  pattern?: RegExp
  custom?: (value: unknown) => string | null
}

interface EntityRules {
  [field: string]: ValidationRule
}

const rules: Record<string, EntityRules> = {
  login: {
    username: { required: true },
    password: { required: true },
  },
  role: {
    nombre: { required: true, maxLength: 255 },
  },
  categoria: {
    nombre: { required: true, maxLength: 255 },
  },
  marca: {
    nombre: { required: true, maxLength: 255 },
  },
  objeto: {
    codigo: { maxLength: 12 },
    nombre: { required: true, maxLength: 150 },
    modelo: { maxLength: 250 },
    serie: { maxLength: 50 },
  },
  movimiento: {
    user_id: { required: true },
    objeto_id: { required: true },
    registrado_por: { required: true },
    tipo_movimiento: { required: true },
    fecha_hora: { required: true },
  },
  user: {
    username: { required: true, maxLength: 255 },
    dni: { required: true, size: 8 },
    nombres: { required: true, maxLength: 120 },
    apellidos: { required: true, maxLength: 120 },
    whatsapp_number: { maxLength: 15 },
    password: { required: true, minLength: 8 },
  },
  userUpdate: {
    username: { required: true, maxLength: 255 },
    dni: { required: true, size: 8 },
    nombres: { required: true, maxLength: 120 },
    apellidos: { required: true, maxLength: 120 },
    whatsapp_number: { maxLength: 15 },
    password: { minLength: 8 },
  },
}

function validateField(
  field: string,
  value: unknown,
  rule: ValidationRule,
  labels: Record<string, string>,
): string | null {
  const label = labels[field] ?? field

  if (rule.required) {
    if (value === null || value === undefined) {
      return `El campo ${label} es obligatorio.`
    }
    if (typeof value === 'string' && !value.trim()) {
      return `El campo ${label} es obligatorio.`
    }
    if (Array.isArray(value) && value.length === 0) {
      return `El campo ${label} es obligatorio.`
    }
  }

  if (value === null || value === undefined || value === '') {
    return null
  }

  if (rule.maxLength && typeof value === 'string' && value.length > rule.maxLength) {
    return `El campo ${label} no debe exceder los ${rule.maxLength} caracteres.`
  }

  if (rule.minLength && typeof value === 'string' && value.length < rule.minLength) {
    return `El campo ${label} debe tener al menos ${rule.minLength} caracteres.`
  }

  if (rule.size && typeof value === 'string' && value.length !== rule.size) {
    return `El campo ${label} debe tener exactamente ${rule.size} caracteres.`
  }

  if (rule.pattern && typeof value === 'string' && !rule.pattern.test(value)) {
    return `El formato del campo ${label} no es válido.`
  }

  if (rule.custom) {
    return rule.custom(value)
  }

  return null
}

type InertiaForm = ReturnType<typeof useForm>

const defaultLabels: Record<string, string> = {
  nombre: 'nombre',
  username: 'usuario',
  password: 'contraseña',
  dni: 'DNI',
  nombres: 'nombres',
  apellidos: 'apellidos',
  whatsapp_number: 'número de WhatsApp',
  role_id: 'rol',
  marca_id: 'marca',
  categoria_id: 'categoría',
  objeto_id: 'objeto',
  user_id: 'usuario',
  registrado_por: 'registrado por',
  tipo_movimiento: 'tipo de movimiento',
  fecha_hora: 'fecha y hora',
  codigo: 'código',
  modelo: 'modelo',
  descripcion: 'descripción',
  serie: 'serie',
}

export function useValidation(
  form: InertiaForm,
  entity: string,
  customLabels?: Record<string, string>,
) {
  const entityRules = rules[entity] ?? {}
  const labels = { ...defaultLabels, ...customLabels }

  function validate(): boolean {
    form.clearErrors()
    let valid = true

    for (const [field, fieldRules] of Object.entries(entityRules)) {
      const value = (form as Record<string, unknown>)[field]
      const error = validateField(field, value, fieldRules, labels)

      if (error) {
        form.setError(field, error)
        valid = false
      }
    }

    return valid
  }

  function validateSingleField(field: string): boolean {
    const rule = entityRules[field]
    if (!rule) return true

    const value = (form as Record<string, unknown>)[field]
    const error = validateField(field, value, rule, labels)

    if (error) {
      form.setError(field, error)
      return false
    }

    form.clearErrors(field)
    return true
  }

  return {
    validate,
    validateSingleField,
  }
}
