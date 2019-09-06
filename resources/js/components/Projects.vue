<template>
    <div>
        <h2>My Projects</h2>

        <project-modal></project-modal>

        <project-create-button></project-create-button>

        <div v-if="projects.length">
            <projects-grid :projects="projects"></projects-grid>
        </div>

        <div v-else>
            No projects saved.
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: ['projects_json'],
    components: {
        'project-create-button': require('./projectsChildren/ProjectCreateButton.vue').default,
        'projects-grid': require('./projectsChildren/ProjectsGrid.vue').default,
        'project-modal': require('./projectsChildren/ProjectCreateEditModal.vue').default
    },
    computed: {
        projects() {
            return JSON.parse(this.projects_json);
        }
    }
}
</script>