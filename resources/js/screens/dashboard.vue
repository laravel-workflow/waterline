<script type="text/ecmascript-6">
    import _ from 'lodash';
    import moment from 'moment';

    export default {
        components: {},


        /**
         * The component's data.
         */
        data() {
            return {
                stats: {},
                ready: false,
            };
        },


        /**
         * Prepare the component.
         */
        mounted() {
            moment.relativeTimeThreshold('ss', 1);

            document.title = "Waterline - Dashboard";

            this.refreshStatsPeriodically();
        },


        /**
         * Clean after the component is destroyed.
         */
        destroyed() {
            clearTimeout(this.timeout);
        },

        methods: {
            /**
             * Load the general stats.
             */
            loadStats() {
                return this.$http.get(Waterline.basePath + '/api/stats')
                    .then(response => {
                        this.stats = response.data;

                        if (_.values(response.data.wait)[0]) {
                            this.stats.max_wait_time = _.values(response.data.wait)[0];
                            this.stats.max_wait_queue = _.keys(response.data.wait)[0].split(':')[1];
                        }
                    });
            },

            /**
             * Refresh the stats every period of time.
             */
            refreshStatsPeriodically() {
                Promise.all([
                    this.loadStats(),
                ]).then(() => {
                    this.ready = true;

                    this.timeout = setTimeout(() => {
                        this.refreshStatsPeriodically();
                    }, 5000);
                });
            },

            duration(start, end) {
                return moment(end).from(moment(start), true)
            },
        }
    }
</script>

<template>
    <div>
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5>Overview</h5>
            </div>

            <div class="card-bg-secondary">
                <div class="d-flex">
                    <div class="w-25 border-right border-bottom">
                        <div class="p-4">
                            <small class="text-uppercase">Flows Per Minute</small>

                            <h4 class="mt-4 mb-0">
                                {{ stats.flows_per_minute ? stats.flows_per_minute.toLocaleString() : 0 }}
                            </h4>
                        </div>
                    </div>

                    <div class="w-25 border-right border-bottom">
                        <div class="p-4">
                            <small class="text-uppercase">Flows Past Hour</small>

                            <h4 class="mt-4 mb-0">
                                {{ stats.flows_past_hour ? stats.flows_past_hour.toLocaleString() : 0 }}
                            </h4>
                        </div>
                    </div>

                    <div class="w-25 border-right border-bottom">
                        <div class="p-4">
                            <small class="text-uppercase">Exceptions Past Hour</small>

                            <h4 class="mt-4 mb-0">
                                {{ stats.exceptions_past_hour ? stats.exceptions_past_hour.toLocaleString() : 0 }}
                            </h4>
                        </div>
                    </div>

                    <div class="w-25 border-bottom">
                        <div class="p-4">
                            <small class="text-uppercase">Failed Flows Past Week</small>

                            <h4 class="mt-4 mb-0">
                                {{ stats.failed_flows_past_week ? stats.failed_flows_past_week.toLocaleString() : 0 }}
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="w-25 border-right">
                        <div class="p-4 mb-0">
                            <small class="text-uppercase">Total Flows</small>

                            <h4 class="mt-4">
                                {{ stats.flows ? stats.flows.toLocaleString() : 0 }}
                            </h4>
                        </div>
                    </div>

                    <div class="w-25 border-right">
                        <div class="p-4 mb-0">
                            <small class="text-uppercase">Max Wait Time</small>

                            <h4 class="mt-4 mb-0">
                                {{ stats.max_wait_time_workflow ? duration(stats.max_wait_time_workflow.updated_at, new Date()) : '-' }}
                            </h4>

                            <small class="mt-1" v-if="stats.max_wait_time_workflow">
                                (<router-link :title="stats.max_wait_time_workflow.class" :to="{ name: (stats.max_wait_time_workflow.status === 'failed' ? 'failed' : (stats.max_wait_time_workflow.status === 'completed' ? 'completed' : 'running')) + '-flows-preview', params: { flowId: stats.max_wait_time_workflow.id }}">{{ flowBaseName(stats.max_wait_time_workflow.class) }}</router-link>)
                            </small>
                        </div>
                    </div>

                    <div class="w-25 border-right">
                        <div class="p-4 mb-0">
                            <small class="text-uppercase">Max Duration</small>

                            <h4 class="mt-4 mb-0">
                                {{ stats.max_duration_workflow ? duration(stats.max_duration_workflow.created_at, stats.max_duration_workflow.updated_at) : '-' }}
                            </h4>

                            <small class="mt-1" v-if="stats.max_duration_workflow">
                                (<router-link :title="stats.max_duration_workflow.class" :to="{ name: (stats.max_duration_workflow.status === 'failed' ? 'failed' : (stats.max_duration_workflow.status === 'completed' ? 'completed' : 'running')) + '-flows-preview', params: { flowId: stats.max_duration_workflow.id }}">{{ flowBaseName(stats.max_duration_workflow.class) }}</router-link>)
                            </small>
                        </div>
                    </div>

                    <div class="w-25">
                        <div class="p-4 mb-0">
                            <small class="text-uppercase">Max Exceptions</small>

                            <h4 class="mt-4 mb-0">
                                {{ stats.max_exceptions_workflow ? stats.max_exceptions_workflow.exceptions_count.toLocaleString() : 0 }}
                            </h4>

                            <small class="mt-1" v-if="stats.max_exceptions_workflow">
                                (<router-link :title="stats.max_exceptions_workflow.class" :to="{ name: (stats.max_exceptions_workflow.status === 'failed' ? 'failed' : (stats.max_exceptions_workflow.status === 'completed' ? 'completed' : 'running')) + '-flows-preview', params: { flowId: stats.max_exceptions_workflow.id }}">{{ flowBaseName(stats.max_exceptions_workflow.class) }}</router-link>)
                            </small>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</template>
