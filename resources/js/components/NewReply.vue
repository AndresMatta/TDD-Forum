<template>
    <div>
        <div v-if="signedIn">
            <div class="form-group">
                <textarea name="body" id="body" class="form-control" placeholder="Have something to say?" row="5" v-model="body" required>
                </textarea>
            </div>
                
            <button type="submit" class="btn btn-success" @click="addReply" :disabled="loading">Post</button>
        </div>

         <p class="text-center" v-else>Please <a href="/login">sign in</a> to participate in this discussion.</p>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                body: '',
                loading: false
            }
        },
        computed: {
            signedIn() {
                return window.App.signedIn;
            }
        },
        methods: {
            addReply() {
                this.loading = true;
                axios.post(location.pathname + '/replies', { body: this.body })
                    .then(({data}) => {
                        this.body = '';
                        this.$emit('created', data);    
                        flash('Your reply has been published.');
                        this.loading = false;
                    })
                    .catch((error) => {
                        this.body = '';
                        flash(error.response.data, 'danger');
                        this.loading = false;
                    });
            }
        }
    }
</script>
