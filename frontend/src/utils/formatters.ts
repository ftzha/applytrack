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
