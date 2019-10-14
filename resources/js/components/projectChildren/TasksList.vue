<template>
    <div>
        <div class="card text-left" v-for="task in tasks" :key="task.id">
            <div class="card-body d-flex justify-content-between">
                <span :style="getStyle(task.completed)">{{ task.name }}</span>
                <div>
                    <span :hidden="task.completed">{{ timer(task.due_by) }}</span>
                    <input type="checkbox" id="taskDueBy" name="completed" @click="changeStatus(task)" :checked="task.completed">
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        @click="handleEdit(task)"
                        data-toggle="modal"
                        data-target="#createTaskModal"
                    >
                        Edit
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" @click="handleDelete(task.id)">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { timeRemainingString } from '../../helpers/dateFunctions';
import { setInterval } from 'timers';
export default {
    props: ['tasks'],
    data() {
        return {
            startSeconds: Date.now() / 1000,
            timerId: null,
            secondsElapsed: 0
        }
    },
    beforeMount() {
        this.timerId = setInterval(() => {
            this.secondsElapsed = Date.now() / 1000 - this.startSeconds;
        }, 1000 * 20);
    },
    destroyed() {
        clearInterval(this.timerId);
    },
    methods: {
        handleEdit(task) {
            this.$parent.$emit('editTask', task);
        },
        handleDelete(taskId) {
            if (!confirm('Are you sure you want to delete this task?')) {
                return;
            }
            axios.delete(`/task/${taskId}`)
                .then(() => {
                    window.location.reload();
                }).catch(err => {
                    console.log(err);
                });
        },
        timer(dueBy) {
            return timeRemainingString(dueBy, this.secondsElapsed, this.startSeconds);
        },
        changeStatus(task) {
            axios.put(`/task/${task.id}`, {completed: !task.completed})
                .then(() => {
                    window.location.reload();
                }).catch(err => {
                    console.log(err);
                })
        },
        getStyle(completed) {
            return completed ? 'text-decoration: line-through' : '';
        }
    }
    
}
</script>

<style lang="scss" scoped>
input {
    margin: 0 5px;
}
</style>