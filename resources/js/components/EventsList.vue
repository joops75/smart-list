<template>
    <div>
        <h3 v-if="type === 'projects'">All Updates</h3>
        <h3 v-else>Task Updates</h3>
        <div v-for="event in events" :key="event.id">
            {{ event.model }} "{{ event.name }}" {{ event.type }} at {{ formattedDate(event.created_at) }}
        </div>
        <button class="btn btn-secondary" @click="getMoreEvents" :disabled="eventsListFull">
            {{ eventsListFull ? 'All updates shown' : 'Show more...' }}
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
                console.log(data); // returns less data than expected after deleting tasks
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
        }
    },
    computed: {
        eventsListFull() {
            return this.events.length < 10 || this.full;
        }
    }
}
</script>