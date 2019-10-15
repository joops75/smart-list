<template>
    <div>

        <project-modal></project-modal>
        
        <div class="row">
            <div class="col-sm-9">
                <h2>My Projects</h2>

                <br>
                
                <div class="d-flex justify-content-center group">
                    <button type="button" class="btn btn-secondary" :data-url="`/project`" @click="navigate" :disabled="get_type === 'all'">View All Projects</button>
                    <button type="button" class="btn btn-secondary" :data-url="`/project?get=incomplete`" @click="navigate" :disabled="get_type === 'incomplete'">View Incomplete Projects</button>
                    <button type="button" class="btn btn-secondary" :data-url="`/project?get=completed`" @click="navigate" :disabled="get_type === 'completed'">View Completed Projects</button>
                    <button type="button" class="btn btn-secondary" :data-url="`/project?get=empty`" @click="navigate" :disabled="get_type === 'empty'">View Empty Projects</button>
                </div>

                <br>

                <project-create-button></project-create-button>

                <br>

                <div v-if="projects.length">
                    <projects-grid :projects="projects"></projects-grid>

                    <br>
                    
                    <button type="button" class="btn btn-danger" @click="deleteProjects">Delete {{ capitalizeFirstLetter(get_type) }} Projects</button>
                </div>
                
                <div v-else>
                    {{ noProjectsMessage }}
                </div>

                <br>
            </div>
            
            <div class="col-sm-3">
                <events-list type="projects"></events-list>
            </div>
        </div>
        
    </div>
</template>

<script>
import axios from 'axios';
import { capitalizeFirstLetter } from '../helpers/textFunctions';

export default {
    props: ['projects_json', 'get_type'],
    components: {
        'project-create-button': require('./projectsChildren/ProjectCreateButton.vue').default,
        'projects-grid': require('./projectsChildren/ProjectsGrid.vue').default,
        'project-modal': require('./projectsChildren/ProjectModal.vue').default,
        'events-list': require('./EventsList.vue').default
    },
    computed: {
        projects() {
            return JSON.parse(this.projects_json);
        },
        noProjectsMessage() {
            switch (this.get_type) {
                case 'completed':   return 'No completed projects.';
                case 'incomplete':  return 'No incomplete projects.';
                case 'empty':       return 'No empty projects.';
                default:            return 'No projects saved.';
            }
        }
    },
    methods: {
        navigate(e) {
            window.location.assign(e.target.dataset.url);
        },
        deleteProjects() {
            if (!confirm(`Are you sure you want to delete ${this.get_type} projects?`)) {
                return;
            }

            axios.delete('/project/0', {
                params: {
                    get: this.get_type
                }
            }).then(() => {
                window.location.reload();
            }).catch(err => {
                console.log(err);
            });
        },
        capitalizeFirstLetter(text) {
            return capitalizeFirstLetter(text);
        }
    }
}
</script>