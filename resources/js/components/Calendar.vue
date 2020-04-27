<template>
    <div>
        <v-snackbar
            v-model="snackbar.visible"
            :color="snackbar.color"
            :top = true
        >
        {{ snackbar.text }}
        <v-btn
            snackbar.text
            @click="snackbar.visible = false"
        >
            Close
        </v-btn>
        </v-snackbar>
        <v-row>
            <!-- FORM -->
            <v-form
                ref="form"
                v-model="valid"
                lazy-validation
            >
                <v-text-field
                v-model="eventName"
                :counter="10"
                :rules="eventNameRules"
                label="Event Name"
                required
                ></v-text-field>

                <v-date-picker
                v-model="dates"
                range
                :rules="dateRangeRules">
                </v-date-picker>

                <v-col>
                    <v-select
                        v-model="selectedDays"
                        :items="options.days"
                        :rules="daysRules"
                        item-text="label"
                        item-value="value"
                        label="Select Days"
                        multiple
                    ></v-select>
                </v-col>

                <v-btn @click="save()" :disabled="!valid" color="primary">Save</v-btn>
            </v-form>
            <!-- Start Calendar -->
            <Fullcalendar
            :plugins="calendarPlugins"
            :header="{
                left: 'title',
                center: 'dayGridMonth, timeGridWeek, timeGridDay, listWeek',
                right: 'prev today next'
            }"
            :selectable="true"
            :events="EVENTS"
            />
        </v-row>
    </div>
</template>
<style>
    form{
        width: 30%;
        float: "left";
    }

    .fc-unthemed{
        float:right;
        width: 70%;
    }
</style>
<script>

require('@fullcalendar/core/main.min.css')
require('@fullcalendar/daygrid/main.min.css')
require('@fullcalendar/timegrid/main.min.css')

import Fullcalendar from '@fullcalendar/vue'
import DayGridPlugin from '@fullcalendar/daygrid'
import TimeGridPlugin from '@fullcalendar/timegrid'
import InteractionPlugin from '@fullcalendar/interaction'
import ListPlugin from '@fullcalendar/list'

import {mapGetters} from 'vuex'

export default {
    data: () => ({
        dates: [],
        valid: false,
        eventName: '',
        eventNameRules: [
            v => !!v || 'Event Name is required',
            v => (v && v.length <= 12) || 'Name must be less than 10 characters',
        ],
        dateRange: [],
        dateRangeRules: [
            v => !!v || 'Date is required',
        ],
        selectedDays: [],
        daysRules: [
            (v) => !!v || 'Day/s is required',
        ],
        options: {
            days: [
                {label:"Monday", value:1},
                {label:"Tuesday", value:2},
                {label:"Wednesday", value:3},
                {label:"Thursday", value:4},
                {label:"Friday", value:5},
                {label:"Saturday", value:6},
                {label:"Sunday", value:7},
            ],
        },

        events: [],

        calendarPlugins: [
            DayGridPlugin,
            TimeGridPlugin,
            InteractionPlugin,
            ListPlugin
        ],

        snackbar:{
            visible:false,
            color:'',
            text:'',
        },
    }),
    created() {
        this.loadEvents();
    },
    watch: {
      multiple (val) {
        if (val) this.selectedDays = [this.selectedDays]
        else this.selectedDays = this.selectedDays[0] || ''
      },
    },
    components: {Fullcalendar},
    methods: {
      validate () {
        this.$refs.form.validate()
      },
      reset () {
        this.$refs.form.reset()
      },
      loadEvents() {
        let self = this;
        let page_url = 'api/v1/events';
        axios.get(page_url).then(function(response) {
            self.events = response.data.data;
            self.events.forEach(function(item){
                self.$store.commit("ADD_EVENT", {
                    id: event.id,
                    title: item.event_name,
                    start: item.date,
                    end: item.date,
                    allDay: true,
                })
            });

        }).catch(function (error) {

        });
      },
      save() {
        const self = this;
        self.validate();
        if(self.validate){
            let payload = {
            event_name: self.eventName,
            date: self.dates,
            days: self.selectedDays
            };

            axios.post('/api/v1/events',payload).then(function(response) {
                console.log(response);
                self.$store.commit('RESET_EVENT',{})
                self.loadEvents();
                self.snackbar.visible = true;
                self.snackbar.color = 'green';
                self.snackbar.text = response.data.message;
            }).catch(function (error) {
                console.log(error);
                self.snackbar.visible = true;
                self.snackbar.color = 'red';
                self.snackbar.text = error.response.data.message;
            });
        }

    },
    handleSelect(arg) {
        console.log(arg)
        this.$store.commit("ADD_EVENT", {
            id: (new Date()).getTime(),
            title: "Something",
            start: arg.start,
            end: arg.end,
            allDay: arg.allDay
        })
    },
    handleEventClick (arg) {
        console.log(arg)
        this.$modal.show(EventModal, {
            text: "This is from the component",
            event: arg.event
        })
    }
    },
    computed: {
      dateRangeText () {
        return this.dates.join(' ~ ')
      },
      ...mapGetters(["EVENTS"])
    },
}
</script>
