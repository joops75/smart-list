<template>
    <div>
        <div v-for="task in tasks" :key="task.id">
            <div>{{ task.name }}</div>
            <div>{{ dueBy(task.due_by) }}</div>
        </div>
    </div>
</template>

<script>
import { dueBy } from '../../helpers/dateFunctions';
export default {
    props: ['tasks'],
    methods: {
        dueBy(dateStr) {
            return dueBy(dateStr);
        }
    }
    
}
</script>