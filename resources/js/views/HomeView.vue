<template>
  <v-dialog v-model="dialogedit">
    <v-card class="p-3">
      <v-card-title> جار العمل علي تعديل التحضرة </v-card-title>
    </v-card>
  </v-dialog>
  <!-- end dialogedit -->
  <v-dialog v-model="dialogOpenIn">
    <v-card class="p-3">
      <v-card-title><span> تفاصيل تحضيرة </span> {{ PreparationData.name }}</v-card-title>
      <v-chip class="ma-1" color="primary" label>
        <span class="p-1"> وقت التحضير </span>
        <span style="text-wrap: wrap" class="p-1">{{ date(PreparationData.actual_time) }}</span>
      </v-chip>
      <v-chip class="ma-1" color="primary" label>
        <span class="p-1">{{ PreparationData.cont_hours }}</span> <span class="p-1"> ساعة </span>
      </v-chip>
      <v-chip class="ma-1" color="primary" label>
        <span class="p-1">{{ PreparationData.ppm }}</span>
        <span class="p-1"> ppm </span>
      </v-chip>
      <v-chip class="ma-1" color="primary" label>
        <span class="p-1">{{ PreparationData.slices_ton }}</span>
        <span class="p-1"> طن شرائح </span>
      </v-chip>
      <v-chip class="ma-1" color="primary" label>
        <span class="p-1">{{ PreparationData.quantity }}</span>
        <span class="p-1"> كيلو خام </span>
      </v-chip>
      <v-chip class="ma-1" color="primary" label>
        <span class="p-1"> الوردية </span>
        <span class="p-1">{{ PreparationData.shift }}</span>
      </v-chip>
      <v-chip class="ma-1" color="primary" label>
        <span class="p-1"> منذ </span>
        <span class="p-1">{{ timeSince(PreparationData.actual_time) }}</span>
        <span class="p-1"> ساعة </span>
      </v-chip>
      <v-chip class="ma-1" color="primary" label>
        <span class="p-1"> متبقي </span>
        <span class="p-1">{{
          timeSince2(PreparationData.actual_time, PreparationData.cont_hours)
        }}</span>
        <span class="p-1"> ساعة </span>
      </v-chip>

      <v-chip v-if="PreparationData.user_name" class="ma-1" color="primary" label>
        <span class="p-1"> بواسطة </span>
        <span class="p-1">{{ PreparationData.user_name }}</span>
      </v-chip>
      <v-chip class="ma-1" color="primary" label>
        <span class="p-1"> تم الانشاء </span>
        <span class="p-1">{{ date(PreparationData.created) }}</span>
      </v-chip>
      <v-chip
        class="ma-1"
        v-if="PreparationData.created != PreparationData.updated"
        color="primary"
        label
      >
        <span class="p-1"> تم التحديث </span>
        <span class="p-1">{{ date(PreparationData.updated) }}</span>
      </v-chip>
    </v-card>
  </v-dialog>
  <div style="position: relative">
    <div class="addPreparation">
      <addPreparationComponent />
    </div>
    <!-- <speedDialComponent/> -->
    <v-card class="mx-auto" width="100%" variant="tonal" elevation="9">
      <routerLink to="/user/edit">
        <v-card-title>
          {{ store.user.name }}
          <v-card-subtitle>
            {{ store.user.email }}
          </v-card-subtitle>
        </v-card-title>
      </routerLink>
      <router-link
        :to="`/station/${station.id}`"
        v-for="(station, i) in store.user.stations"
        :key="i"
      >
        <v-chip class="m-1">
          {{ station.name }}
        </v-chip>
      </router-link>
    </v-card>
    <!-- slide-group link  -->
    <v-slide-group class="mt-4" show-arrows>
      <v-slide-group-item v-for="(routerList, i) in routerLists" :key="i">
        <router-link v-if="routerList.meta.show" :to="routerList.path"
          ><v-chip color="primary" variant="outlined" class="m-1">
            {{ routerList.meta.titleAr }}</v-chip
          ></router-link
        >
      </v-slide-group-item>
    </v-slide-group>
    <!-- <v-row class="div-balance text-center">
      <v-col cols="12">
        <router-link to="/vacations">
          <div class="box-balance">
            <div class="item-balance">الاعتيادية</div>
            <div class="item-balance">{{ store.regular }}</div>
          </div>
        </router-link>
      </v-col>
    </v-row> -->
    <v-row class="my-3">
      <v-col cols="12" md="4" v-for="typePrep in typePreparationData" :key="typePrep.id">
        <v-card class="p-39j" elevation="5" v-if="typePreparationData">
          <div class="btn_action">
            <v-btn
              color="red-lighten-6"
              icon="mdi-eye-outline"
              variant="text"
              @click="openInFun(typePrep)"
            ></v-btn>
            <v-btn
              color="red-lighten-6"
              icon="mdi-text-box-edit-outline"
              variant="text"
              @click="editInFun()"
            ></v-btn>
          </div>
          <div class="box_details p-1">
            <p>ppm {{ typePrep.ppm }}</p>
            <p>{{ typePrep.user_name }}</p>
          </div>
          <div class="preparation_box">
            <div class="percentage">
              <p>{{ typePrep.name }}</p>
              <p>% {{ percentageResalt(100 - typePrep.percentage) }}</p>
            </div>
            <div
              :style="{
                height: 100 - typePrep.percentage + '%',
                'background-color': 100 - typePrep.percentage <= 10 ? 'red' : '#14cc14',
              }"
              class="preparation_fill"
            ></div>
          </div>
          <div class="mt-2 d-flex">
            <v-chip class="ma-1" color="primary" label>
              <span class="p-1">{{ typePrep.cont_hours }}</span> <span class="p-1"> ساعة </span>
            </v-chip>
            <v-chip class="ma-1" color="primary" label>
              <span class="p-1"> متبقي </span>
              <span class="p-1">{{ timeSince2(typePrep.actual_time, typePrep.cont_hours) }}</span>
            </v-chip>
            <!-- <v-chip color="primary" label> <span class="p-1"> وقت التحضير </span> </v-chip> -->
            <v-chip class="ma-1" color="primary" label>
              <span style="text-wrap: wrap" class="p-1">{{ date(typePrep.actual_time) }}</span>
            </v-chip>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>
<script setup>
import moment from 'moment';
import { onMounted, ref } from 'vue';
import { usemainStore } from '@/store/mainStore';
import addPreparationComponent from '../components/preparation/addPreparationComponent.vue';
import { useRouter } from 'vue-router';
const router = useRouter();
const routerLists = router.getRoutes();
const store = usemainStore();
const typePreparationData = ref([]);
const PreparationData = ref('');
const dialogOpenIn = ref(false);
const dialogedit = ref(false);
const hoursToAdd = 1; // عدد الساعات المراد إضافتها
const dateN = new Date(); // الحصول على التاريخ والوقت الحاليين
const oneHourInMilliseconds = 3600000; // تحويل ساعة إلى مللي ثانية
dateN.setTime(dateN.getTime() + hoursToAdd * oneHourInMilliseconds);
const now = moment();
const diff = moment(dateN).diff(now, 'hours');
function openInFun(typePrep) {
  PreparationData.value = typePrep;
  dialogOpenIn.value = true;
}
function editInFun() {
  dialogedit.value = true;
}
function percentageResalt(percentage) {
  return percentage.toFixed();
}
function timeSince(time) {
  //  return moment(time).fromNow();
  const now = moment();
  return -moment(time).diff(now, 'hours', true).toFixed(2);
}
function timeSince2(actual_time, cont_hours) {
  const now = moment();
  const actua_time = -moment(actual_time).diff(now, 'hours', true);
  return (cont_hours - actua_time).toFixed(2);
}
function date(d) {
  return moment(d).format('dddd :- h:mm A - MM/DD ');
}
onMounted(() => {
  store.getAbsences();
  store.getUser();
  store.getReports();
  typePreparFunc();
});

function typePreparFunc() {
  store.getTypePre('latestPreparationActual.user').then(() => {
    for (let i = 0; i < store.typePreparation.length; i++) {
      if (!store.typePreparation[i].latest_preparation_actual) continue;
      const newPreparationData = {
        id: store.typePreparation[i].id,
        name: store.typePreparation[i].name,
        updated: store.typePreparation[i].latest_preparation_actual.updated_at,
        created: store.typePreparation[i].latest_preparation_actual.created_at,

        ppm: store.typePreparation[i].latest_preparation_actual.ppm,
        shift: store.typePreparation[i].latest_preparation_actual.shift,
        quantity: store.typePreparation[i].latest_preparation_actual.quantity,
        slices_ton: store.typePreparation[i].latest_preparation_actual.slices_ton,
        actual_time: store.typePreparation[i].latest_preparation_actual.actual_time,
        cont_hours: store.typePreparation[i].latest_preparation_actual.cont_hours,
        user_name: store.typePreparation[i].latest_preparation_actual.user?.name || 'N/A',
        hoursDifference: calculateHoursDifference(
          store.typePreparation[i].latest_preparation_actual.actual_time
        ).toFixed(2),
        percentage: (
          (calculateHoursDifference(
            store.typePreparation[i].latest_preparation_actual.actual_time
          ) /
            store.typePreparation[i].latest_preparation_actual.cont_hours) *
          100
        ).toFixed(2),
      };
      typePreparationData.value.push(newPreparationData);
      store.overlay = false;
    }
    function calculateHoursDifference(actualTime) {
      const dataTime = new Date(actualTime);
      const currentTime = new Date();
      const timeDifference = currentTime - dataTime;
      return timeDifference / (1000 * 60 * 60);
    }
  });
}
</script>
<style scoped>
.addPreparation {
  position: fixed;
  top: 85%;
  left: 67%;
  z-index: 999;
}
a {
  text-decoration: none;
}
.preparation_box {
  position: relative;
  width: 100%;
  height: 20vh;
  border-bottom: 5px solid blue;
  border-left: 5px solid blue;
  border-right: 5px solid blue;
  border-radius: 0 0 20px 20px;
  overflow: hidden;
}

.preparation_fill {
  position: absolute;
  bottom: 0px;
  width: 100%;
  box-sizing: unset;
}
.pre_fill_red {
  background-color: red;
}
.pre_fill_green {
  background-color: #14cc14;
}
.percentage {
  position: absolute;
  width: 100%;
  font-weight: bold;
  top: 2px;
  text-align: center;
  position: absolute;
  display: flex;
  z-index: 1;
  flex-direction: column;
  justify-content: space-around;
  align-items: center;
  flex-wrap: wrap;
}
.btn_action {
  display: flex !important;
  z-index: 9;
  flex-direction: column;
  align-items: center;
  position: absolute;
  top: 3px;
  right: 9px;
}
.box_details {
  display: flex !important;
  z-index: 9;
  flex-direction: column;
  align-items: center;
  position: absolute;
  top: 3px;
  left: 9px;
}

.d-flex {
  display: flex !important;
  align-content: stretch;
  flex-wrap: wrap;
}
</style>
