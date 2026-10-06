export function formatSalary(
  amount: string | null,
  currency: string | null,
) {
  if (!amount) return null

  return new Intl.NumberFormat('en-MY', {
    style: 'currency',
    currency: currency ?? 'MYR',
    maximumFractionDigits: 0,
  }).format(Number(amount))
}

export function formatSalaryRange(
  min: string | null,
  max: string | null,
  currency: string | null,
) {
  if (min && max) {
    return `${formatSalary(min, currency)} ~ ${formatSalary(max, currency)}`
  }

  if (min) {
    return `From ${formatSalary(min, currency)}`
  }

  if (max) {
    return `Up to ${formatSalary(max, currency)}`
  }

  return 'Not specified'
}

export function formatDate(date: string | null) {
  if (!date) return 'Not specified'

  return new Intl.DateTimeFormat('en-MY', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
  }).format(new Date(date))
}

export function formatDateTime(date: string) {
  return new Intl.DateTimeFormat('en-MY', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(new Date(date))
}

export function formatDateTimeLocal(date: string | null) {
  if (!date) return ''

  return date.slice(0, 16)
}

export function formatLocalDateTime(date: string | null) {
  if (!date) return 'Not specified'

  const [datePart, timePart] = date.split('T')

  if (!datePart || !timePart) {
    return 'Not specified'
  }

  const [year, month, day] = datePart.split('-')
  const [hour, minute] = timePart.split(':')

  if (!year || !month || !day || !hour || !minute) {
    return 'Not specified'
  }

  const value = new Date(
    Number(year),
    Number(month) - 1,
    Number(day),
    Number(hour),
    Number(minute),
  )

  return new Intl.DateTimeFormat('en-MY', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(value)
}

export function parseLocalDateTime(date: string) {
  const [datePart, timePart] = date.split('T')

  if (!datePart || !timePart) {
    return null
  }

  const [year, month, day] = datePart.split('-').map(Number)
  const [hour, minute] = timePart.split(':').map(Number)

  if (
    !year ||
    !month ||
    !day ||
    hour === undefined ||
    minute === undefined
  ) {
    return null
  }

  return new Date(
    year,
    month - 1,
    day,
    hour,
    minute,
  )
}

export function formatDisplayValue(
  value: string | null | undefined,
) {
  if (!value) return '-'

  return value
    .replace(/_/g, ' ')
    .replace(/\b\w/g, (char) => char.toUpperCase())
}
