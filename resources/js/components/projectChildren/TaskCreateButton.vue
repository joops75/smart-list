<template>
    <div>
        <button type="button" class="btn btn-primary" @click="handleClick" data-toggle="modal" data-target="#createTaskModal">
            Create New Task
        </button>
    </div>
</template>

<script>
export default {
    methods: {
        handleClick() {
            this.$parent.$emit('createTask');
        }
    }
}
</script>