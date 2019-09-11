<template>
    <div>
        <div v-for="task in tasks" :key="task.id">
            <span :style="getStyle(task.completed)">
                <span>{{ task.name }}</span>
                <span>{{ dueBy(task.due_by) }}</span>
                <span>{{ timer(task.due_by) }}</span>
            </span>
            <input type="checkbox" id="taskDueBy" name="completed" @click="changeStatus(task)" :checked="task.completed">
            <button
                type="button"
                class="btn btn-primary"
                @click="handleEdit(task)"
                data-toggle="modal"
                data-target="#createTaskModal"
            >
                Edit
            </button>
            <button type="button" class="btn btn-danger" @click="handleDelete(task.id)">
                Delete
            </button>
        </div>
    </div>
</template>

<script>
import { dueBy, timeRemainingString } from '../../helpers/dateFunctions';
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
        dueBy(dateStr) {
            return dueBy(dateStr);
        },
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