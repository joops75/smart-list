<template>
    <div class="d-flex justify-content-start flex-wrap group">
        <div class="card" v-for="project in projects" v-bind:key="project.id">
            <div class="card-body d-flex flex-column justify-content-between text-left">
                <div><h5 class="card-title"><a :href="'/project/' + project.id">{{ excerpt(project.title, 100) }}</a></h5></div>
                <p>{{ excerpt(project.description, 100) }}</p>
                <div class="d-flex justify-content-center group">
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
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { excerpt } from '../../helpers/textFunctions';

export default {
    props: ['projects'],
    methods: {
        handleEdit(project) {
            this.$parent.$emit('editProject', project);
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
        },
        excerpt(text, limit) {
            return excerpt(text, limit);
        }
    }
}
</script>

<style lang="scss" scoped>
.card {
    width: 250px;
    min-height: 200px;
}
</style>