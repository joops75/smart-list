<template>
    <div>
        <div v-for="task in tasks" :key="task.id">
            <span>{{ task.name }}</span>
            <span>{{ dueBy(task.due_by) }}</span>
            <span>{{ timer(task.due_by) }}</span>
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
import { dueBy, timer } from '../../helpers/dateFunctions';
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
        }, 1000);
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
            const secondsRemaining = Math.floor(timer(dueBy, this.secondsElapsed) - this.startSeconds);
            const minute = 60;
            const hour = minute * 60;
            const day = hour * 24;
            const week = day * 7;
            const month = day * 31;
            const year = day * 365;
            let value;
            let unit;
            if (secondsRemaining > year || secondsRemaining < -year) { value = Math.ceil(secondsRemaining / year); unit = 'year'; }
            else if (secondsRemaining > month || secondsRemaining < -month) { value = Math.ceil(secondsRemaining / month); unit = 'month'; }
            else if (secondsRemaining > week || secondsRemaining < -week) { value = Math.ceil(secondsRemaining / week); unit = 'week'; }
            else if (secondsRemaining > day || secondsRemaining < -day) { value = Math.ceil(secondsRemaining / day); unit = 'day'; }
            else if (secondsRemaining > hour || secondsRemaining < -hour) { value = Math.ceil(secondsRemaining / hour); unit = 'hour'; }
            else if (secondsRemaining > minute || secondsRemaining < -minute) { value = Math.ceil(secondsRemaining / minute); unit = 'minute'; }
            else { value = secondsRemaining; unit = 'second'; }

            if (value > 0) {
                return `due in under ${value} ${unit}${value === 1 ? '' : 's'}`;
            } else if (value === 0) {
                return 'due now';
            } else {
                return `due over ${-value} ${unit}${-value === 1 ? '' : 's'} ago`;
            }
        }
    }
    
}
</script>