<template>
    <div id="createTaskModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ mode }} Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="taskName">Name</label>
                            <input type="text" class="form-control" id="taskName" name="name" v-model="task.name">
                        </div>
                        <div class="form-group">
                            <label for="taskDueBy">Due By</label>
                            <input type="text" class="form-control" id="taskDueBy" name="due_by" v-model="task.due_by">
                        </div>
                        <div class="form-group">
                            <label for="taskDueBy">Completed</label>
                            <input type="checkbox" class="form-control" id="taskDueBy" name="completed" :checked="task.completed">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="handleSubmit" :disabled="!isValid">{{ mode }} Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { dueBy, getYearMonthDay } from '../../helpers/dateFunctions';
class Task {
    constructor(task) {
        this.name = task.name || '';
        this.due_by = task.due_by || getYearMonthDay(new Date()) + ' 17:00';
        this.completed = task.completed || false;
        this.id = task.id || '';
    }
}
export default {
    data() {
        return {
            task: {},
            mode: ''
        }
    },
    mounted() {
        this.$parent.$on('createTask', () => {
            this.mode = 'Create';
            this.task = new Task({});
        });
        this.$parent.$on('editTask', task => {
            this.mode = 'Edit';
            this.task = new Task({ ...task, due_by: dueBy(task.due_by) });
        });
    },
    methods: {
        handleSubmit() {
            if (this.mode == 'Create') {
                axios.post('/task', {
                    name: this.task.name,
                    due_by: new Date(this.task.due_by),
                    completed: this.task.completed,
                    project_id: window.location.pathname.match(/\d+/)[0]
                }).then(res => {
                    window.location.reload();
                }).catch(err => {
                    console.log(err);
                });
            } else if (this.mode == 'Edit') {
                axios.put(`/task/${this.task.id}`, {
                    name: this.task.name,
                    due_by: new Date(this.task.due_by),
                    completed: this.task.completed
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
            return this.task.name && this.task.due_by && new Date(this.task.due_by).getDate();
        }
    }
}
</script>