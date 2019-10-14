<template>
    <div>
        <h3 v-if="type === 'projects'">All Updates</h3>
        <h3 v-else>Task Updates</h3>
        <ul class="text-left" v-for="event in events" :key="event.id">
            <li>{{ modelType(event.task_id) }} "{{ event.name }}" {{ event.type }} at {{ formattedDate(event.created_at) }}</li>
        </ul>
        <button class="btn btn-secondary" @click="getMoreEvents" :hidden="eventsListFull">
            Show more...
        </button>
        <br><br>
        <button class="btn btn-danger" @click="deleteEvents" :hidden="!events.length">
            {{ getDeleteEventsMessage() }}
        </button>
    </div>
</template>

<script>
import axios from 'axios';
import { dueBy } from '../helpers/dateFunctions';

export default {
    props: ['type', 'projectId'],
    mounted() {
        const url = this.type === 'projects' ? '/event' : `/event?get=tasks&projectId=${this.projectId}`;
        axios.get(url)
            .then(({ data }) => {
                this.events = data;
            })
            .catch(err => {
                console.log(err);
            });
    },
    data() {
        return {
            events: [],
            full: false
        }
    },
    methods: {
        formattedDate(dataStr) {
            return dueBy(dataStr);
        },
        getMoreEvents() {
            const skip = this.events.length;
            const url = this.type === 'projects' ? `/event?skip=${skip}` : `/event?get=tasks&projectId=${this.projectId}&skip=${skip}`;
            axios.get(url)
                .then(({ data }) => {
                    if (data.length) {
                        this.events = this.events.concat(data);
                        if (data.length < 10) this.full = true;
                        else this.full = false;
                    } else {
                        this.full = true;
                    }
                })
                .catch(err => {
                    console.log(err);
                });
        },
        deleteEvents() {
            if (!confirm(`Are you sure you want to ${this.getDeleteEventsMessage().toLowerCase()}?`)) {
                return;
            }

            const url = this.type === 'projects' ? '/event' : `/event?delete=tasks&projectId=${this.projectId}`;
            axios.delete(url)
                .then(() => {
                    this.events = [];
                })
                .catch(err => {
                    console.log(err);
                });
        },
        getDeleteEventsMessage() {
            return this.type === 'projects' ? 'Delete all updates' : 'Delete all task updates';
        },
        modelType(taskId) {
            return taskId ? 'Task' : 'Project';
        }
    },
    computed: {
        eventsListFull() {
            return this.events.length < 10 || this.full;
        }
    }
}
</script>