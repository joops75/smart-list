<template>
    <div>
        <div v-for="project in projects" v-bind:key="project.id">
            {{ project.title }}
            <button
                type="button"
                class="btn btn-primary"
                @click="handleClick(project)"
                data-toggle="modal"
                data-target="#createProjectModal"
            >
                Edit
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: ['projects'],
    methods: {
        handleClick(project) {
            this.$parent.$emit('edit', project);
        }
    }
}
</script>