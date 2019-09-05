<template>
    <div>
        <h2>My Projects</h2>

        <project-modal :mode="mode" :handleSubmit="handleSubmit" :currentProject="currentProject"></project-modal>

        <project-create-button :handleClick="handleClick"></project-create-button>

        <div v-if="projects.length">
            <projects-grid :projects="projects" :handleClick="handleClick"></projects-grid>
        </div>

        <div v-else>
            No projects saved.
        </div>
    </div>
</template>

<script>
class Project {
    constructor(title = '', description = '') {
        this.title = title;
        this.description = description;
    }
}
export default {
    props: ['projects_json'],
    data() {
        return {
            mode: null,
            currentProject: new Project()
        }
    },
    components: {
        'project-create-button': require('./projectsChildren/ProjectCreateButton.vue').default,
        'projects-grid': require('./projectsChildren/ProjectsGrid.vue').default,
        'project-modal': require('./projectsChildren/ProjectCreateEditModal.vue').default
    },
    computed: {
        projects() {
            return JSON.parse(this.projects_json);
        }
    },
    methods: {
        handleClick(e) {
            const mode = e.target.dataset.mode;
            this.mode = mode;
            console.log(mode);
            if (mode == 'Create') {
                this.currentProject = new Project();
            } else if (mode == 'Edit') {
                this.currentProject = new Project(e.target.dataset.projectTitle, e.target.dataset.projectDescription);
            }
        },
        handleSubmit() {
            console.log('mode', this.mode);
        }
    }
}
</script>