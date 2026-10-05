<script setup lang="ts">
import type {
  ApplicationFormData,
  StatusOption,
} from '@/types/application'

const props = defineProps<{
  form: ApplicationFormData
  statusOptions: StatusOption[]
  validationErrors: Record<string, string[]>
  submitting: boolean
  submitLabel: string
}>()

const emit = defineEmits<{
  submit: []
}>()

function handleSubmit() {
  emit('submit')
}
</script>

<template>
  <form
    class="card application-form"
    @submit.prevent="handleSubmit"
  >
    <div class="form-group">
      <label for="company_name">Company</label>

      <input
        id="company_name"
        v-model="props.form.company_name"
        type="text"
        placeholder="e.g. Acme Technologies"
      />

      <p
        v-if="props.validationErrors.company_name"
        class="field-error"
      >
        {{ props.validationErrors.company_name[0] }}
      </p>
    </div>

    <div class="form-group">
      <label for="position">Position</label>

      <input
        id="position"
        v-model="props.form.position"
        type="text"
        placeholder="e.g. Software Engineer"
      />

      <p
        v-if="props.validationErrors.position"
        class="field-error"
      >
        {{ props.validationErrors.position[0] }}
      </p>
    </div>

    <div class="form-group">
      <label for="location">Location</label>

      <input
        id="location"
        v-model="props.form.location"
        type="text"
        placeholder="e.g. Kuala Lumpur"
      />

      <p
        v-if="props.validationErrors.location"
        class="field-error"
      >
        {{ props.validationErrors.location[0] }}
      </p>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="employment_type">Employment Type</label>

        <select
          id="employment_type"
          v-model="props.form.employment_type"
        >
          <option value="">Not specified</option>
          <option value="full-time">Full-time</option>
          <option value="part-time">Part-time</option>
          <option value="contract">Contract</option>
          <option value="internship">Internship</option>
        </select>

        <p
          v-if="props.validationErrors.employment_type"
          class="field-error"
        >
          {{ props.validationErrors.employment_type[0] }}
        </p>
      </div>

      <div class="form-group">
        <label for="work_mode">Work Mode</label>

        <select
          id="work_mode"
          v-model="props.form.work_mode"
        >
          <option value="">Not specified</option>
          <option value="on-site">On-site</option>
          <option value="hybrid">Hybrid</option>
          <option value="remote">Remote</option>
        </select>

        <p
          v-if="props.validationErrors.work_mode"
          class="field-error"
        >
          {{ props.validationErrors.work_mode[0] }}
        </p>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="salary_min">Minimum Salary</label>

        <input
          id="salary_min"
          v-model="props.form.salary_min"
          type="number"
          min="0"
          placeholder="e.g. 5000"
        />

        <p
          v-if="props.validationErrors.salary_min"
          class="field-error"
        >
          {{ props.validationErrors.salary_min[0] }}
        </p>
      </div>

      <div class="form-group">
        <label for="salary_max">Maximum Salary</label>

        <input
          id="salary_max"
          v-model="props.form.salary_max"
          type="number"
          min="0"
          placeholder="e.g. 7000"
        />

        <p
          v-if="props.validationErrors.salary_max"
          class="field-error"
        >
          {{ props.validationErrors.salary_max[0] }}
        </p>
      </div>
    </div>

    <div class="form-group">
      <label for="currency">Currency</label>

      <select
        id="currency"
        v-model="props.form.currency"
      >
        <option value="MYR">MYR — Malaysian Ringgit</option>
        <option value="SGD">SGD — Singapore Dollar</option>
        <option value="USD">USD — US Dollar</option>
      </select>

      <p
        v-if="props.validationErrors.currency"
        class="field-error"
      >
        {{ props.validationErrors.currency[0] }}
      </p>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="source">Source</label>

        <input
          id="source"
          v-model="props.form.source"
          type="text"
          placeholder="e.g. JobStreet"
        />

        <p
          v-if="props.validationErrors.source"
          class="field-error"
        >
          {{ props.validationErrors.source[0] }}
        </p>
      </div>

      <div class="form-group">
        <label for="job_url">Job URL</label>

        <input
          id="job_url"
          v-model="props.form.job_url"
          type="url"
          placeholder="https://..."
        />

        <p
          v-if="props.validationErrors.job_url"
          class="field-error"
        >
          {{ props.validationErrors.job_url[0] }}
        </p>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="status">Status</label>

        <select
          id="status"
          v-model="props.form.status"
        >
          <option
            v-for="status in props.statusOptions"
            :key="status.value"
            :value="status.value"
          >
            {{ status.label }}
          </option>
        </select>

        <p
          v-if="props.validationErrors.status"
          class="field-error"
        >
          {{ props.validationErrors.status[0] }}
        </p>
      </div>

      <div class="form-group">
        <label for="applied_at">Date Applied</label>

        <input
          id="applied_at"
          v-model="props.form.applied_at"
          type="date"
        />

        <p
          v-if="props.validationErrors.applied_at"
          class="field-error"
        >
          {{ props.validationErrors.applied_at[0] }}
        </p>
      </div>
    </div>

    <div class="form-group">
      <label for="notes">Notes</label>

      <textarea
        id="notes"
        v-model="props.form.notes"
        rows="5"
        placeholder="Anything useful about this application..."
      ></textarea>

      <p
        v-if="props.validationErrors.notes"
        class="field-error"
      >
        {{ props.validationErrors.notes[0] }}
      </p>
    </div>

    <div class="form-actions">
      <RouterLink
        to="/applications"
        class="btn btn-secondary"
      >
        Cancel
      </RouterLink>

      <button
        type="submit"
        class="btn btn-primary"
        :disabled="props.submitting"
      >
        {{ props.submitting ? 'Saving...' : props.submitLabel }}
      </button>
    </div>
  </form>
</template>

<style scoped>
.application-form {
  max-width: 720px;
  display: grid;
  gap: 24px;
}

.form-group {
  display: grid;
  gap: 8px;
}

.form-group label {
  font-size: 14px;
  font-weight: 600;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 0 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  background: #ffffff;
  color: #1f2937;
}

.form-group input,
.form-group select {
  min-height: 42px;
}

.form-group textarea {
  padding-top: 10px;
  padding-bottom: 10px;
  resize: vertical;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  border-color: #2563eb;
  outline: none;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  align-items: start;
}

.field-error {
  margin: 0;
  color: #b91c1c;
  font-size: 13px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 8px;
}

@media (max-width: 640px) {
  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
