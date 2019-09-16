<template>
    <div>
        <h2>My Projects</h2>

        <project-modal></project-modal>

        <project-create-button></project-create-button>

        <div>
            <button type="button" class="btn btn-secondary" :data-url="`/project`" @click="navigate" :disabled="get_type === 'all'">View All Projects</button>
            <button type="button" class="btn btn-secondary" :data-url="`/project?get=incomplete`" @click="navigate" :disabled="get_type === 'incomplete'">View Incomplete Projects</button>
            <button type="button" class="btn btn-secondary" :data-url="`/project?get=completed`" @click="navigate" :disabled="get_type === 'completed'">View Completed Projects</button>
            <button type="button" class="btn btn-secondary" :data-url="`/project?get=empty`" @click="navigate" :disabled="get_type === 'empty'">View Empty Projects</button>
        </div>

        <div v-if="projects.length">
            <projects-grid :projects="projects"></projects-grid>
        </div>

        <div v-else>
            {{ noProjectsMessage }}
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: ['projects_json', 'get_type'],
    components: {
        'project-create-button': require('./projectsChildren/ProjectCreateButton.vue').default,
        'projects-grid': require('./projectsChildren/ProjectsGrid.vue').default,
        'project-modal': require('./projectsChildren/ProjectCreateEditModal.vue').default
    },
    computed: {
        projects() {
            return JSON.parse(this.projects_json);
        },
        noProjectsMessage() {
            if (this.get_type === 'completed') {
                return 'No completed projects.'
            } else if (this.get_type === 'incomplete') {
                return 'No incomplete projects.'
            } else if (this.get_type === 'empty') {
                return 'No empty projects.'
            } else {
                return 'No projects saved.'
            }
        }
    },
    methods: {
        navigate(e) {
            window.location.assign(e.target.dataset.url);
        }
    }
}
</script>