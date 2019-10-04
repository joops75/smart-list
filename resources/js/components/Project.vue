<template>
    <div>
        <div class="row">
            <div class="col-9">
                <h2>{{ project.title }}</h2>

                <task-modal></task-modal>

                <task-create-button></task-create-button>

                <div>
                    <button type="button" class="btn btn-secondary" :data-url="`/project/${project.id}`" @click="navigate" :disabled="get_type === 'all'">View All Tasks</button>
                    <button type="button" class="btn btn-secondary" :data-url="`/project/${project.id}?get=incomplete`" @click="navigate" :disabled="get_type === 'incomplete'">View Incomplete Tasks</button>
                    <button type="button" class="btn btn-secondary" :data-url="`/project/${project.id}?get=completed`" @click="navigate" :disabled="get_type === 'completed'">View Completed Tasks</button>
                </div>

                <div v-if="tasks.length">
                    <tasks-list :tasks="tasks"></tasks-list>
                    <button type="button" class="btn btn-danger" @click="deleteCompletedTasks" :disabled="!completedTasks">Delete All Completed Tasks</button>
                </div>

                <div v-else>
                    No tasks saved.
                </div>
                
                <a href="/project">Back to My Projects page</a>
            </div>
            
            <div class="col-3">
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
        'events-list': require('./EventsList.vue').default
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
        }
    }
}
</script>