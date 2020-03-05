<template>
  <div class="home">
    <ProjectTable  />
  </div>
</template>

<script>
// @ is an alias to /src
import ProjectTable from '@/components/ProjectTable.vue'

export default {
  name: 'home',
  components: {
    ProjectTable
  }
}
</script>
