<template>
    <div>
        <button type="button" class="btn btn-primary" @click="handleClick" data-toggle="modal" data-target="#createProjectModal">
            Create New Project
        </button>
    </div>
</template>

<script>
export default {
    methods: {
        handleClick() {
            this.$parent.$emit('create');
        }
    }
}
</script>