<template>
    <div>
        <div v-for="project in projects" v-bind:key="project.id">
            <a :href="'/project/' + project.id">{{ project.title }}</a>
            <button
                type="button"
                class="btn btn-primary"
                @click="handleEdit(project)"
                data-toggle="modal"
                data-target="#createProjectModal"
            >
                Edit
            </button>
            <button type="button" class="btn btn-danger" @click="handleDelete(project.id)">
                Delete
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: ['projects'],
    methods: {
        handleEdit(project) {
            this.$parent.$emit('edit', project);
        },
        handleDelete(projectId) {
            if (!confirm('Are you sure you want to delete this project?')) {
                return;
            }
            axios.delete(`/project/${projectId}`)
                .then(() => {
                    window.location.reload();
                }).catch(err => {
                    console.log(err);
                });
        }
    }
}
</script>