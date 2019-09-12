<template>
    <div>
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
        </div>

        <div v-else>
            No tasks saved.
        </div>
    </div>
</template>

<script>
export default {
    props: ['project_json', 'tasks_json', 'get_type'],
    components: {
        'task-modal': require('./projectChildren/TaskModal.vue').default,
        'task-create-button': require('./projectChildren/TaskCreateButton.vue').default,
        'tasks-list': require('./projectChildren/TasksList.vue').default
    },
    methods: {
        navigate(e) {
            window.location.assign(e.target.dataset.url);
        }
    },
    computed: {
        project() {
            return JSON.parse(this.project_json);
        },
        tasks() {
            return JSON.parse(this.tasks_json);
        }
    }
}
</script>