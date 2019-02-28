<template>
    <button type="submit" :class="classes" @click="toggle">
        <span class="fas fa-heart"></span>
        <span v-text="favoritesCount"></span>
    </button>    
</template>

<script>
    export default {
        props: ['reply'],
        data() {
            return {
                favoritesCount: this.reply.favoritesCount,
                isFavorited: this.reply.isFavorited
            }
        },
        computed: {
            classes() {
                return  ['btn', this.isFavorited ? 'btn-primary' : 'btn-default'];
            }
        },
        methods: {
            toggle() {
                this.isFavorited ? this.unfavorite() : this.favorite();
            },
            favorite() {
                axios.post('/replies/' + this.reply.id + '/favorites');

                this.isFavorited = true;
                this.favoritesCount++;
            },
            unfavorite() {
                axios.delete('/replies/' + this.reply.id + '/favorites');

                this.isFavorited = false;
                this.favoritesCount--;
            }
        }
    }
</script>
