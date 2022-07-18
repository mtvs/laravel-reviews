<template>
    <span class="rating-stars">
       <span v-for="i in 5" 
            @click="$emit('score-selected', i)">
            <i :class="i <= score ? 'icon-star' : 'icon-star-empty'"></i>
       </span>
    </span>
</template>

<script>
export default {
    props: [
        'score',
    ]
}
</script>