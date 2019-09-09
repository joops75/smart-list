<template>
    <div>
        <h2>{{ project.title }}</h2>

        <task-modal></task-modal>

        <task-create-button></task-create-button>

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
    props: ['project_json', 'tasks_json'],
    components: {
        'task-modal': require('./projectChildren/TaskModal.vue').default,
        'task-create-button': require('./projectChildren/TaskCreateButton.vue').default,
        'tasks-list': require('./projectChildren/TasksList.vue').default
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