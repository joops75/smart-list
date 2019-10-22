<template>
    <div id="createProjectModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ mode }} Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="projectTitle">Title</label>
                            <input type="text" class="form-control" id="projectTitle" name="title" v-model="project.title">
                        </div>
                        <div class="form-group">
                            <label for="projectDescription">Description</label>
                            <textarea class="form-control" id="projectDescription" rows="3" name="description" v-model="project.description"></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="handleSubmit" :disabled="!isValid">{{ mode }} Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
class Project {
    constructor(project) {
        this.title = project.title || '';
        this.description = project.description || '';
        this.id = project.id || '';
    }
}
export default {
    data() {
        return {
            project: {},
            mode: ''
        }
    },
    mounted() {
        this.$parent.$on('createProject', () => {
            this.mode = 'Create';
            this.project = new Project({});
        });
        this.$parent.$on('editProject', project => {
            this.mode = 'Edit';
            this.project = new Project(project);
        });
    },
    methods: {
        handleSubmit() {
            if (this.mode == 'Create') {
                axios.post('/project', {
                    title: this.project.title,
                    description: this.project.description
                }).then(res => {
                    window.location.reload();
                }).catch(err => {
                    console.log(err);
                });
            } else if (this.mode == 'Edit') {
                axios.put(`/project/${this.project.id}`, {
                    title: this.project.title,
                    description: this.project.description
                }).then(res => {
                    window.location.reload();
                }).catch(err => {
                    console.log(err);
                });
            }
        }
    },
    computed: {
        isValid() {
            return this.project.title && this.project.title.length < 256  && this.project.description;
        }
    }
}
</script>