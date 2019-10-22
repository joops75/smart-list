<template>
    <div>

        <project-modal></project-modal>

        <task-modal></task-modal>

        <div class="row">
            <div class="col-sm-9 text-center">
                <div class="card text-white bg-info mb-3">
                    <h2 class="card-header text-left">Project: {{ project.title }}</h2>
                    <div class="card-body">
                        <p class="card-text text-left">{{ project.description }}</p>
                    </div>
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="handleEditProject(project)"
                        data-toggle="modal"
                        data-target="#createProjectModal"
                    >
                        Edit
                    </button>
                    <button type="button" class="btn btn-danger" @click="handleDeleteProject(project.id)">
                        Delete
                    </button>
                </div>

                <br>

                <div class="d-flex justify-content-center group">
                    <button type="button" class="btn btn-secondary" :data-url="`/project/${project.id}`" @click="navigate" :disabled="get_type === 'all'">View All Tasks</button>
                    <button type="button" class="btn btn-secondary" :data-url="`/project/${project.id}?get=incomplete`" @click="navigate" :disabled="get_type === 'incomplete'">View Incomplete Tasks</button>
                    <button type="button" class="btn btn-secondary" :data-url="`/project/${project.id}?get=completed`" @click="navigate" :disabled="get_type === 'completed'">View Completed Tasks</button>
                </div>

                <br>

                <task-create-button></task-create-button>

                <br>

                <div v-if="tasks.length">
                    <tasks-list :tasks="tasks"></tasks-list>

                    <br>

                    <button type="button" class="btn btn-danger" @click="deleteCompletedTasks" :hidden="!completedTasks">Delete All Completed Tasks</button>
                </div>

                <div v-else>
                    No tasks saved.
                </div>

                <br>
                
                <a href="/project">Back to My Projects page</a>

                <br><br>
            </div>
            
            <div class="col-sm-3">
                <events-list :projectId="project.id"></events-list>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: ['project_json', 'tasks_json', 'get_type'],
    components: {
        'task-modal': require('./projectChildren/TaskModal.vue').default,
        'task-create-button': require('./projectChildren/TaskCreateButton.vue').default,
        'tasks-list': require('./projectChildren/TasksList.vue').default,
        'events-list': require('./EventsList.vue').default,
        'project-modal': require('./projectsChildren/ProjectModal.vue').default
    },
    data() {
        return {
            project: {},
            tasks: [],
            completedTasks: false
        }
    },
    created() {
        this.project = JSON.parse(this.project_json);
        this.tasks = JSON.parse(this.tasks_json);
        this.completedTasks = this.tasks.some(task => task.completed);
    },
    methods: {
        navigate(e) {
            window.location.assign(e.target.dataset.url);
        },
        deleteCompletedTasks() {
            if (!confirm('Are you sure you want to delete all completed tasks for this project?')) {
                return;
            }
            
            axios.delete(`/task/0`, {
                params: {
                    deleteAllCompletedTasksOfAssociatedProject: true,
                    projectId: this.project.id
                }
            }).then(() => {
                window.location.reload();
            }).catch(err => {
                console.log(err);
            });
        },
        handleEditProject(project) {
            this.$emit('editProject', project);
        },
        handleDeleteProject(projectId) {
            if (!confirm('Are you sure you want to delete this project?')) {
                return;
            }
            axios.delete(`/project/${projectId}`)
                .then(() => {
                    window.location.assign('/project');
                }).catch(err => {
                    console.log(err);
                });
        },
    }
}
</script>