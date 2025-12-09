package com.dhanuka.indhan.MenuScreenActivity

import android.content.Context
import android.text.TextUtils
import android.util.Log
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.Activity.EditTicketMVP.Model.ImageTicketResponse
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.NewTicketBean
import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import com.dhanuka.indhan.Activity.TicketDetailMVP.EndTicketMVP.Model.EndTicketBean
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel
import com.dhanuka.indhan.Activity.offlineClaimTicket.ManualTicketClaimImage
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ArrivalDepartureModelOffline
import com.dhanuka.indhan.DataBase.TableDataModel.ImageOfflineModel
import com.dhanuka.indhan.DemoRequestActivity.models.CreateRequestDemoResponse
import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestOfflineModel
import com.dhanuka.indhan.DemoRequestEvaluated.models.GoDownData
import com.dhanuka.indhan.DemoRequestEvaluated.models.GoDownResponse
import com.dhanuka.indhan.DemoRequestEvaluated.models.RequestMaterialModel
import com.dhanuka.indhan.MarkVisits.MarkVisitBean
import com.dhanuka.indhan.MarkVisits.MarkVisitRequestModel
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.*
import com.dhanuka.indhan.Model.*
import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.ActivityDurationModel
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.AddIfcFarmer
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.SelfParticipationActivity.Model.ParticipationPlannedResponse
import com.dhanuka.indhan.addfarmers.AddFarmerSuccessModel
import com.dhanuka.indhan.addfarmers.model.AddFarmerModel
import com.dhanuka.indhan.bdoddlist.BdoDdListBean
import com.dhanuka.indhan.farmerlist.FarmerBean
import com.dhanuka.indhan.institutional.model.MarkArrivalDepartureDistributor
import com.dhanuka.indhan.institutional.model.TypeDesignationFLModel
import com.dhanuka.indhan.institutional.model.influencers.*
import com.dhanuka.indhan.institutional.model.sugarMill.*
import com.dhanuka.indhan.institutional.model.teaEstate.*
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.AppLogFile
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.LogModel
import com.dhanuka.indhan.util.Utility
import com.google.firebase.analytics.FirebaseAnalytics
import com.google.gson.Gson
import com.google.gson.GsonBuilder
import com.google.gson.JsonObject
import kotlinx.coroutines.*
import okhttp3.MediaType
import okhttp3.MultipartBody
import okhttp3.RequestBody
import org.greenrobot.eventbus.EventBus
import org.jetbrains.anko.runOnUiThread
import org.joda.time.DateTime
import org.json.JSONArray
import org.json.JSONException
import org.json.JSONObject
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import rx.Single
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.io.File
import java.text.SimpleDateFormat
import java.util.*
import kotlin.collections.set

class OfflineDatabase(val context: Context) {
    private var TAG = "OfflineDatabase"
    private var ctx: Context = context
    private var preference: Preference = Preference(context)
    private var appDatabase: AppDatabase = DatabaseClient.getInstance(context).appDatabase
    private var progressCount = 0
    private lateinit var progressListener: ProgressListener
    private var isLoaderShown = false

    fun checkOfflineDatabase(isLoaderShown: Boolean, progressListener: ProgressListener) {
        this.progressListener = progressListener
        this.isLoaderShown = isLoaderShown
        if (CheckConnectionStatus.isInternetAvailable(context) && isLoaderShown)
            showProgressDialog()
        if (CheckConnectionStatus.isInternetAvailable(context)) {
            getMyVillageList()
            checkLocalStateList()
            checkLocalDistrictList()
            checkLocalTehsilList()
            checkLocalVillageList()
            getDDList()
            getDemoRequestListApi()
            getDataSourceList()
            getTicketData()
            getGoDownList()
            getTerritoryList()
        }
    }

    private fun getTerritoryList() {
        rx.Observable.fromCallable {
            getLastHitTimeData("v3/getAllterritories")
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
            return@onErrorReturn ApiTimeTableModel()
        }.subscribe {
            val time: String = it?.updatedAt ?: "0"
            getAllTerritory(time)
        }
    }

    private fun getAllTerritory(time: String) {
        if (CheckConnectionStatus.isInternetAvailable(context)) {
            val hashMap: HashMap<String, Any> = HashMap()
            hashMap["apiToken"] = preference.getString("token")
            hashMap["updated_at"] = time
            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .getAllTerritoryList(hashMap).subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<TerritoryResponse>() {
                    override fun onCompleted() {}

                    override fun onError(e: Throwable?) {
                        logEventCustom(
                            e?.localizedMessage ?: "unknown error occurred",
                            null,
                            "v3/getAllterritories"
                        )
                    }

                    override fun onNext(it: TerritoryResponse?) {
                        it?.let {
                            if (it.status_code == 200) {
                                if (it.data != null) {
                                    insertTeritoryListInDb(it.data)
                                }
                            } else if (it.status_code == 409) {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v3/getAllterritories"
                                )
                                EventBus.getDefault().post("logout")
                            } else {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v3/getAllterritories"
                                )
                            }
                        }
                    }

                })
        }
    }

    private fun insertTeritoryListInDb(data: List<TerritoryResponse.TerritoryData>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data.forEach {
                    appDatabase.territoryDao().insert(it)
                }
            }.run {
                withContext(Dispatchers.Main) {
                    "v3/getAllterritories".updateTimeOnDb()
                }
            }
        }
    }

    private fun getMyVillageList() {
        var time: String? = null
        rx.Observable.fromCallable {
            getLastHitTimeData("v3/villageList")
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
            return@onErrorReturn ApiTimeTableModel()
        }.subscribe {
            time = it?.updatedAt ?: "0"
            if (CheckConnectionStatus.isInternetAvailable(context)) {
                val query = HashMap<String, Any>()
                query["ECN"] = preference.getString("ecn")
                query["apiToken"] = preference.getString("token")
                query["updated_date"] = "$time"
                RetrofitClient.mvpClient
                    .create(ApiServiceRequest::class.java).villageListOffline(query)
                    .subscribeOn(Schedulers.newThread())
                    .observeOn(AndroidSchedulers.mainThread())
                    .subscribe(object : Subscriber<MyVillageBean>() {
                        override fun onCompleted() {

                        }

                        override fun onError(e: Throwable?) {
//                            Log.d(TAG, "getMyVillageList: onError");
                            logEventCustom(
                                e?.localizedMessage ?: "Custom Message -> unknown error occurred",
                                null,
                                "villageList"
                            )
                        }

                        override fun onNext(it: MyVillageBean?) {
//                            Log.d(TAG, "getMyVillageList: ${it?.statusCode}");
//                            Log.d(TAG, "getMyVillageList-Village id: ${(Gson().toJson(it?.data) ?: "phata")}");
                            if (it != null) {
                                when (it.statusCode) {
                                    200 -> {
                                        insertMyVillageListInDb(it.data)
                                    }
                                    409 -> {
                                        logEventCustom(
                                            it.message,
                                            null,
                                            "villageList"
                                        )
                                        EventBus.getDefault().post("logout")
                                    }
                                    else -> {
                                        logEventCustom(
                                            it.message,
                                            null,
                                            "villageList"
                                        )
                                    }
                                }
                            }
                        }

                    })
            }
        }
    }

    private fun showProgressDialog() {
        if (progressListener != null)
            progressListener.showProgressDialog()
    }

    private fun dismissProgressDialog() {
        if ((progressCount == 8 || progressCount == 9) && isLoaderShown) {
            if (ctx != null && progressListener != null) {
                ctx.runOnUiThread {
                    progressListener.dismissProgressDialog()
                }
            }
        }
    }

    private fun updateProgress(count: Int) {
        if (isLoaderShown) {
            if (ctx != null && progressListener != null) {
                ctx.runOnUiThread {
                    progressListener.updateProgress(count)
                }
            }
        }

    }

    private fun checkLocalStateList() {
        rx.Observable.fromCallable {
            getLastHitTimeData("v3/newAllStateList")
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
            return@onErrorReturn ApiTimeTableModel()
        }.subscribe {
            val time: String = it?.updatedAt ?: "0"
            getStateList(time)
        }
    }

    private fun checkLocalDistrictList() {
        rx.Observable.fromCallable {
            getLastHitTimeData("v3/newAllDistrictList")
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
            return@onErrorReturn ApiTimeTableModel()
        }.subscribe {
            val time: String = it?.updatedAt ?: "0"
            getDistrictList(time)
        }
    }

    private fun checkLocalVillageList() {
        rx.Observable.fromCallable {
            getLastHitTimeData("v3/newAllVillageList")
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
            return@onErrorReturn ApiTimeTableModel()
        }.subscribe {
            val time: String = it?.updatedAt ?: "0"
            getVillageList(time)
        }
    }

    private fun checkLocalTehsilList() {
        rx.Observable.fromCallable {
            getLastHitTimeData("v3/newAllTehshilList")
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
            return@onErrorReturn ApiTimeTableModel()
        }.subscribe {
            val time: String = it?.updatedAt ?: "0"
            getTehsilList(time)
        }
    }

    private fun getStateList(time: String) {
        if (CheckConnectionStatus.isInternetAvailable(context)) {
            val hashMap: HashMap<String, Any> = HashMap()
            hashMap["apiToken"] = preference.getString("token")
            hashMap["updated_date"] = time
            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .getNewStateList(hashMap)
                .subscribeOn(Schedulers.newThread()).observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<AllStateBean>() {
                    override fun onCompleted() {

                    }

                    override fun onError(e: Throwable?) {
                        logEventCustom(
                            e?.localizedMessage ?: "unknown error occurred",
                            null,
                            "v2/newAllStateList"
                        )
                    }

                    override fun onNext(it: AllStateBean?) {
                        it?.let {
                            if (it.status_code == 200) {
                                insertStateListInDb(it.data)
                            } else if (it.status_code == 409) {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/newAllStateList"
                                )
                                EventBus.getDefault().post("logout")
                            } else {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/newAllStateList"
                                )
                            }
                        }
                    }

                })
        }
    }

    private fun getDistrictList(time: String) {
        if (CheckConnectionStatus.isInternetAvailable(context)) {
            val hashMap: HashMap<String, Any> = HashMap()
            hashMap["apiToken"] = preference.getString("token")
            hashMap["updated_date"] = time
            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .getNewDistrictList(hashMap).subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<AllDistrictBean>() {
                    override fun onCompleted() {
                    }

                    override fun onError(e: Throwable?) {
                        logEventCustom(
                            e?.localizedMessage ?: "unknown error occurred",
                            null,
                            "v2/newAllDistrictList"
                        )
                    }

                    override fun onNext(it: AllDistrictBean?) {
                        it?.let {
                            if (it.status_code == 200) {
                                insertDistrictListInDb(it.data)
                            } else if (it.status_code == 409) {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/newAllDistrictList"
                                )
                                EventBus.getDefault().post("logout")
                            } else {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/newAllDistrictList"
                                )
                            }
                        }
                    }
                })
        }
    }

    private fun getTicketHistory(ticketId: String) {
        if (CheckConnectionStatus.isInternetAvailable(context)) {
            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .getTicketHistory(preference.getString("token"), ticketId, "0")
                .subscribeOn(Schedulers.io())
                .observeOn(AndroidSchedulers.mainThread()).subscribe(
                    object : Subscriber<TicketHistoryModel>() {
                        override fun onCompleted() {
                        }

                        override fun onError(e: Throwable?) {
                            logEventCustom(
                                e?.localizedMessage ?: "unknown error occurred",
                                null,
                                "v2/ticketHistory"
                            )
                        }

                        override fun onNext(it: TicketHistoryModel?) {
                            it?.let {
                                when (it.statusCode) {
                                    200 -> {
                                        it.data?.let { it1 -> insertTicketHistory(it1) }
                                    }
                                    409 -> {
                                        logEventCustom(
                                            it.message,
                                            null,
                                            "v2/ticketHistory"
                                        )
                                        EventBus.getDefault().post("logout")
                                    }
                                    else -> {
                                        logEventCustom(
                                            it.message,
                                            null,
                                            "v2/ticketHistory"
                                        )
                                    }
                                }
                            }
                        }
                    })
        }
    }

    private fun getTicketData() {
        rx.Observable.fromCallable {
            getLastHitTimeData("v3/ticketHistory/{api_token}/{ticket_number}")
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
            return@onErrorReturn ApiTimeTableModel()
        }.subscribe {
            val ticketId = it?.updatedAt ?: "0"
            getTicketHistory(ticketId)
        }
    }

    private fun getVillageList(time: String) {
        if (CheckConnectionStatus.isInternetAvailable(context)) {
            val hashMap: HashMap<String, Any> = HashMap()
            hashMap["apiToken"] = preference.getString("token")
            hashMap["updated_date"] = time
            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .getNewVillageList(hashMap).subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<AllVillageBean>() {
                    override fun onCompleted() {

                    }

                    override fun onError(e: Throwable?) {
                        logEventCustom(
                            e?.localizedMessage ?: "unknown error occurred",
                            null,
                            "v3/newAllVillageList"
                        )
                    }

                    override fun onNext(it: AllVillageBean?) {
                        it?.let {
                            if (it.status_code == 200) {
                                insertVillageListInDb(it.data)
                            } else if (it.status_code == 409) {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/newAllVillageList"
                                )
                                EventBus.getDefault().post("logout")
                            } else {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/newAllVillageList"
                                )
                            }
                        }
                    }

                })
        }
    }

    private fun getTehsilList(time: String) {
        if (CheckConnectionStatus.isInternetAvailable(context)) {
            val hashMap: HashMap<String, Any> = HashMap()
            hashMap["apiToken"] = preference.getString("token")
            hashMap["updated_date"] = time
            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .getNewTehsilList(hashMap).subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<AllTehsilBean>() {
                    override fun onCompleted() {

                    }

                    override fun onError(e: Throwable?) {
                        logEventCustom(
                            e?.localizedMessage ?: "unknown error occurred",
                            null,
                            "v3/newAllTehshilList"
                        )
                    }

                    override fun onNext(it: AllTehsilBean?) {
                        it?.let {
                            if (it.status_code == 200) {
                                insertTehsilListInDb(it.data)
                            } else if (it.status_code == 409) {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/newAllTehshilList"
                                )
                                EventBus.getDefault().post("logout")
                            } else {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/newAllTehshilList"
                                )
                            }
                        }
                    }

                })
        }
    }

    private fun insertMyVillageListInDb(data: MutableList<MyVillageBean.MyVillageData>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data.forEach {
                    appDatabase.myVillageDao().insert(it)
                }
                if (data.size > 0) {
                    delay(2000)
                    callVillageFarmerData()
                }
            }.run {
                withContext(Dispatchers.Main) {
//                    Log.d(TAG, "getMyVillageList-villageList: ${data.size}")
                    progressCount++
                    updateProgress(1)
                    dismissProgressDialog()
                    "v3/villageList".updateTimeOnDb()
                }
            }
        }
    }

    private fun insertTicketHistory(datum: List<TicketHistoryModel.Datum>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                for (i in datum.indices) {
                    appDatabase.ticketHistoryDao().insert(datum[i])
                }
                progressCount++
            }.run {
                withContext(Dispatchers.Main) {
                    updateProgress(9)
                    dismissProgressDialog()
                    "v3/ticketHistory/{api_token}/{ticket_number}".updateTimeOnDb()
                }
            }
        }
    }

    private fun insertStateListInDb(list: ArrayList<AllStateBean.StateData>) {

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                list.forEach {
                    appDatabase.stateDao().insert(it)
                }
                progressCount++
            }.run {
                withContext(Dispatchers.Main) {
                    updateProgress(2)
                    dismissProgressDialog()
                    "v3/newAllStateList".updateTimeOnDb()
                }
            }
        }
    }

    private fun insertDistrictListInDb(list: ArrayList<AllDistrictBean.DistrictData>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                list.forEach {
                    appDatabase.districtDao().insert(it)
                }
                progressCount++
                updateProgress(3)
                dismissProgressDialog()
            }.run {
                withContext(Dispatchers.Main) {
                    "v3/newAllDistrictList".updateTimeOnDb()
                }
            }
        }
    }

    private fun getGoDownList() {
        GlobalScope.launch {
            val id = preference.getInt("state_id")
            var timeModel: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeModel = getLastHitTimeData("v3/listgodown/{state_id}/{updated_date}")
            }.run {
                val time: String = timeModel?.updatedAt ?: "0"
                if (CheckConnectionStatus.isInternetAvailable(context)) {
                    RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                        .getGoDownList(id.toString())
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<GoDownResponse>() {
                            override fun onCompleted() {
                            }

                            override fun onError(e: Throwable?) {
                                logEventCustom(
                                    e?.localizedMessage ?: "unknown error occurred",
                                    null,
                                    "v3/listgodown"
                                )
                            }

                            override fun onNext(it: GoDownResponse?) {
                                it?.let {
                                    when (it.status_code) {
                                        200 -> {
                                            insertGoDownInDb(it.data)
                                        }
                                        409 -> {
                                            logEventCustom(
                                                it.message,
                                                null,
                                                "v2/listgodown"
                                            )
                                            EventBus.getDefault().post("logout")
                                        }
                                        else -> {
                                            logEventCustom(
                                                it.message,
                                                null,
                                                "v2/listgodown"
                                            )
                                        }
                                    }
                                }
                            }
                        })
                }
            }
        }
    }

    private fun insertGoDownInDb(data: List<GoDownData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data?.forEach {
                    appDatabase.goDownDataDao().insert(it)
                }
            }.run {
                withContext(Dispatchers.Main) {
                    "v3/listgodown/{state_id}/".updateTimeOnDb()
                }
            }
        }
    }

    private fun insertTehsilListInDb(list: List<AllTehsilBean.TehsilData>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                list.forEach {
                    appDatabase.tehsilDao().insert(it)
                }
                progressCount++
                updateProgress(4)
                dismissProgressDialog()
            }.run {
                withContext(Dispatchers.Main) {
                    "v3/newAllTehshilList".updateTimeOnDb()
                }
            }
        }
    }

    private fun insertVillageListInDb(list: List<AllVillageBean.VillageData>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                list.forEach {
                    appDatabase.villageDao().insert(it)
                }
                progressCount++
                updateProgress(5)
                dismissProgressDialog()
            }.run {
                withContext(Dispatchers.Main) {
                    "v3/newAllVillageList".updateTimeOnDb()
                }
            }
        }
    }

    private fun insertDDListInDb(list: List<BdoDdListBean.Data>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.ddListDao().deleteTableData()
                appDatabase.ddListDao().insert(list)
                progressCount++
                updateProgress(6)
                dismissProgressDialog()
            }
        }
    }

    private fun insertDemoRequestListInDb(list: List<DemoRequestList>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.demoRequestListDao().deleteTableData()
                appDatabase.demoRequestListDao().insert(list)
                progressCount++
                updateProgress(7)
                dismissProgressDialog()
            }
        }
    }

    private fun insertDataSourceListInDb(list: List<DataSourceBean>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.dataSourceDao().deleteTableData()
                appDatabase.dataSourceDao().insert(list)
                progressCount++
                dismissProgressDialog()
                updateProgress(8)
            }
        }
    }

    private fun String.updateTimeOnDb() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val timeData = getLastHitTimeData(this@updateTimeOnDb)
                val sdf = SimpleDateFormat(Constants.DATE_TIME_FORMAT)
                val currentDate = sdf.format(Date())
                if (timeData != null) {
                    appDatabase.apiTimeDao().updateTimeInDb(this@updateTimeOnDb, currentDate)
                } else {
                    val timeDataModel = ApiTimeTableModel()
                    timeDataModel.apiName = this@updateTimeOnDb
                    timeDataModel.updatedAt = currentDate
                    appDatabase.apiTimeDao().insert(timeDataModel)
                }
            }
        }
    }

    private fun logEventCustom(mes: String?, _id: String?, fromScreen: String) {
        val bundle = HashMap<String, Any>()
        bundle[FirebaseAnalytics.Param.ITEM_ID] = preference.getString("user_id")
        bundle["Api_Name"] = fromScreen
        if (mes != null) {
            bundle["error"] = mes
        } else {
            bundle["error"] = "unknown error occurred"
        }
        bundle["user_detail"] = preference.getString("user_id")
            .plus("_${preference.getString("ecn")}")
            .plus("_${preference.getString("user_type")}")
            .plus(
                "_${preference.getString("Username")}"
                    .plus("_${preference.getString("designation")}")
            )
        if (_id != null) {
            bundle[FirebaseAnalytics.Param.ITEM_NAME] = "get_farmer_for_village_error"
            bundle[FirebaseAnalytics.Param.CONTENT_TYPE] = "getFarmerForVillage"
            bundle["village_id"] = _id
            Utility.logEvent("OfflineDatabaseClass", "Farmer_Village_error", bundle)

            val logModel = LogModel()
            logModel.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
            val data = bundle["Api_Name"].toString().plus(" | $mes")
                .plus(" | get_farmer_for_village_error id -> $_id")
            logModel.apiData = data
            logModel.apiName = "Farmer_Village_error | ".plus(bundle["Api_Name"].toString())
            val userData = bundle["user_detail"].toString()
            logModel.userData = "\nUSER DATA : -> $userData"
            AppLogFile.writeLineInFile(Gson().toJson(logModel), preference.getString("ecn"))
        } else {
            Utility.logEvent("OfflineDatabaseClass", "failed_api_$fromScreen", bundle)
            val logModel = LogModel()
            logModel.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
            val data = bundle["Api_Name"].toString().plus(" | $mes")
            logModel.apiData = data
            logModel.apiName = "Api Error : failed_api_$fromScreen"
            val userData = bundle["user_detail"].toString()
            logModel.userData = "\nUSER DATA : -> $userData"
            AppLogFile.writeLineInFile(Gson().toJson(logModel), preference.getString("ecn"))
        }
    }

    private fun getFarmersList(_id: String) {
        val query = HashMap<String, Any>()
        query["villageId"] = _id
        query["apiToken"] = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getFarmerList(query).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<FarmerBean>() {
                override fun onCompleted() {
                }

                override fun onError(e: Throwable?) {
//                    Log.d(TAG, "callVillageFarmerData-onError: onError")
                    logEventCustom(
                        e?.localizedMessage ?: "Custom Message -> unknown error occurred",
                        _id,
                        "v2/getFarmerList"
                    )
                }

                override fun onNext(it: FarmerBean?) {
                    if (it != null) {
                        if (it.statusCode == 200) {
//                            Log.d(TAG, "callVillageFarmerData-onSuccess: ${it.data}")
//                            Log.d(TAG, "callVillageFarmerData-onSuccess: villageId->$_id")
                            insertVillageFarmerListInDb(it.data, _id)
                        } else {
                            logEventCustom(
                                it.message,
                                _id,
                                "v2/getFarmerList"
                            )
                        }
                    }
                }
            })
    }

    private fun getLastHitTimeData(apiName: String): ApiTimeTableModel? {
        return appDatabase.apiTimeDao().getApiTimeData(apiName)
    }

    private fun insertVillageFarmerListInDb(
        data: MutableList<FarmerBean.VillageFarmerData>,
        _id: String
    ) {

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val list = ArrayList<FarmerBean.VillageFarmerData>()
                if (data.isNotEmpty()) {
                    data.forEach {
                        val farmer = FarmerBean.VillageFarmerData(
                            acpVisit = it.acpVisit,
                            acres = it.acres,
                            alternateContactNumber = it.alternateContactNumber,
                            callStatus = it.callStatus,
                            contactNumber = it.contactNumber,
                            district = it.district,
                            farmarImage = it.farmarImage,
                            farmerId = it.farmerId,
                            farmerLat = it.farmerLat,
                            farmerLong = it.farmerLong,
                            farmerName = it.farmerName,
                            fatherName = it.fatherName,
                            leadStatus = it.leadStatus,
                            state = it.state,
                            tehsil = it.tehsil,
                            village_id = _id,
                            products = it.products
                        )
                        list.add(farmer)
                    }
                    if (list.size > 0) {
                        list.forEach {
                            appDatabase.allFarmerListDao().insert(it)
                        }
                    }
                }
                appDatabase.myVillageDao().updateVillageData(true, _id)
                callVillageFarmerData()
            }
        }
    }

    private fun callVillageFarmerData() {
        val village: MyVillageBean.MyVillageData? =
            appDatabase.myVillageDao().getSingleVillageData(false)
        appDatabase.myVillageDao().myVillageList
        village?.villageId.let {
            if (!TextUtils.isEmpty(it)) {
//                Log.d(TAG, "callVillageFarmerData-villageId: ${village!!.villageId}")
                getFarmersList(village!!.villageId)
            }
        }
    }

    private fun getDDList() {
        if (CheckConnectionStatus.isInternetAvailable(context)) {
            val model: String = preference.getString("token")
            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getBdoList(model)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<BdoDdListBean>() {
                    override fun onCompleted() {

                    }

                    override fun onError(e: Throwable?) {
                        logEventCustom(
                            e?.localizedMessage ?: "Custom Message -> unknown error occurred",
                            null,
                            "v2/BDOddList"
                        )
                    }

                    override fun onNext(it: BdoDdListBean?) {
                        it?.let {
                            if (it.status_code == 200) {
                                val list = ArrayList<BdoDdListBean.Data>()
                                list.add(BdoDdListBean.Data("", "0", "MySelf", false))
                                list.addAll(it.data)
                                insertDDListInDb(list)
                            } else if (it.status_code == 409) {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/BDOddList"
                                )
                                EventBus.getDefault().post("logout")
                            } else {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/BDOddList"
                                )
                            }
                        }
                    }
                })
        }
    }

    private fun getDemoRequestListApi() {
        if (CheckConnectionStatus.isInternetAvailable(context)) {
            val model: String = preference.getString("token")
            val query = HashMap<String, Any>()
            query["apiToken"] = model
            query["approval"] = "approved"
            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .getDemoRequestList(query).subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<DemoRequestResponse>() {
                    override fun onCompleted() {
                    }

                    override fun onError(e: Throwable?) {
                        logEventCustom(
                            e?.localizedMessage ?: "Custom Message -> unknown error occurred",
                            null,
                            "demoRequestListV1"
                        )
                    }

                    override fun onNext(it: DemoRequestResponse?) {
                        it?.let {
                            if (it.status_code == 200) {
                                insertDemoRequestListInDb(it.data)
                            } else if (it.status_code == 409) {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/demoRequestListV1"
                                )
                                EventBus.getDefault().post("logout")
                            } else {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/demoRequestListV1"
                                )
                            }
                        }
                    }

                })
        }
    }

    private fun getDataSourceList() {
        if (CheckConnectionStatus.isInternetAvailable(context)) {
            val model: String = preference.getString("token")
            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).userNewTicket(model)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<NewTicketBean>() {
                    override fun onCompleted() {}

                    override fun onError(e: Throwable?) {
                        logEventCustom(
                            e?.localizedMessage ?: "Custom Message -> unknown error occurred",
                            null,
                            "v2/newTicketOpen"
                        )
                    }

                    override fun onNext(newTicketBean: NewTicketBean) {
                        newTicketBean.let {
                            if (it.status_code == 200) {
                                insertDataSourceListInDb(newTicketBean.data!!.data_source)
                            } else if (it.status_code == 409) {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/newTicketOpen"
                                )
                                EventBus.getDefault().post("logout")
                            } else {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v2/newTicketOpen"
                                )
                            }
                        }

                    }
                })
        }
    }

    /*
    Get list of all Unplanned Online Synced Tickets. First update the ticket, if required, then close the ticket, if required.
     */
    fun getOnlineClosedTicket(token: String) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val list = appDatabase.ticketHistoryDao().getOnlineUnplannedTicket(false)
                if (list != null) {
                    for (i in list.indices) {
                        val datum = list[i]
                        when {
                            datum.isFarmerRegistered -> {
                                val farmerRegistrationBeans =
                                    appDatabase.farmerRegistrationDao()
                                        .getAllNumberList(datum.ticket, 0)
                                val farmerList = ArrayList<RegisterFarmerContactModel>()
                                for (farmerRegistrationBean in farmerRegistrationBeans) {
                                    val registerFarmerContactModel = RegisterFarmerContactModel()
                                    registerFarmerContactModel.phoneNumber =
                                        farmerRegistrationBean.mobileNumber
                                    registerFarmerContactModel.isDeleted =
                                        farmerRegistrationBean.isDeleted
                                    registerFarmerContactModel.isSync =
                                        farmerRegistrationBean.isSync
                                    registerFarmerContactModel.status =
                                        farmerRegistrationBean.status
                                    registerFarmerContactModel.ticketNumber =
                                        farmerRegistrationBean.ticketId
                                    registerFarmerContactModel.timestamp =
                                        farmerRegistrationBean.timeStamp
                                    registerFarmerContactModel.type = farmerRegistrationBean.type
                                    farmerList.add(registerFarmerContactModel)
                                }
                                appDatabase.ticketHistoryDao()
                                    .isFarmerRegistered(datum.ticket, false)
                                registerFarmer(datum, token, farmerList)
                            }
                            datum.isTicketUpdated -> {
                                appDatabase.ticketHistoryDao().isTicketUpdated(datum.id, false)
                                updateTicket(datum)
                            }
                            datum.isTicketClosed -> {
                                appDatabase.ticketHistoryDao().closeTicket(datum.id, false)
                                val query = HashMap<String, Any>()
                                query["api_token"] = token
                                datum.tID?.let { query["tId"] = it }
                                closeTicket(query)
                            }
                        }
                    }
                }
            }
        }
    }


    /*
    Get list of all Planned Ticket which are Activated Offline.
     */
    fun getPlannedTicketList(token: String) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val list = appDatabase.ticketHistoryDao().getPlannedTicket(true)
                for (i in list.indices) {
                    appDatabase.ticketHistoryDao().updateIsTicketATP(list[i].ticket, false)
                    activatePlannedTicket(token, list[i].plan_atp_id.toString(), list[i])
                }
            }
        }
    }

    /*
    Register the farmer associated with that Ticket Number.
     */
    private fun registerFarmer(
        datum: TicketHistoryModel.Datum,
        token: String,
        list: ArrayList<RegisterFarmerContactModel>
    ) {
        val modelData = ContactSyncModel()
        modelData.apiToken = token
        modelData.contactModelList = list
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java)
            .submitNumberOptimized(modelData)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable?) {
                    logEventCustom(
                        e?.localizedMessage ?: "Custom Message -> unknown error occurred",
                        null,
                        "v2/farmerRegistrationSubmit"
                    )
                }

                override fun onNext(SubmitFarmersBean: CommonModel) {
                    if (datum.isTicketUpdated)
                        updateTicket(datum)
                    else if (datum.isTicketClosed) {
                        val query = HashMap<String, Any>()
                        query["api_token"] = token
                        datum.tID?.let { query["tId"] = it }
                        closeTicket(query)
                    }
                    if (SubmitFarmersBean.status_code != 200) {
                        logEventCustom(
                            SubmitFarmersBean.message,
                            null,
                            "v2/farmerRegistrationSubmit"
                        )
                    }
                }
            })
    }

    /*
    Update the ticket which are updated in offline mode.(Only Online Synced Ticket.)
     */
    private fun updateTicket(item: TicketHistoryModel.Datum) {
        val Token = Preference.getInstance(context).getString("token")
        val query = HashMap<String, Any>()
        query.put("api_token", Token)
        item.tID?.let { query.put("ticId", it) }
        query.put("dsId", item.dataSourceId)
        query.put("sId", item.stateId)
        query.put("dId", item.districtId)
        query.put("tId", item.tehsilId)
        query.put("product_1", item.product1Id)
        query.put("crop_1", item.crop1Id)
        query.put("product_2", item.product2Id)
        query.put("crop_2", item.crop2Id)
        query.put("product_3", item.product3Id)
        query.put("crop_3", item.crop3Id)
        query.put("village", item.village)
        query.put("language_id", item.language_id)
        item.expectedFarmers?.let { query.put("expected_farmers", it) }
        item.expectedPda?.let { query.put("expected_pda", it) }
        query.put("sp_name_1", item.spName1)
        query.put("sp_number_1", item.spNumber1)
        query.put("sp_name_2", item.spName2)
        query.put("sp_number_2", item.spNumber2)
        query.put("sp_name_3", item.spName3)
        query.put("sp_number_3", item.spNumber3)
        query.put("sp_name_4", item.spName4)
        query.put("sp_number_4", item.spNumber4)
        query.put("sp_name_5", item.spName5)
        query.put("sp_number_5", item.spNumber5)
        query.put("sp_name_6", item.spName6)
        query.put("sp_number_6", item.spNumber6)
        query.put("demo_request_id", item.demo_request_id)
        query.put("user_farmer", item.user_farmer)
        updateTicketToServer(query, item, Token)
    }

    private fun updateTicketToServer(
        model: HashMap<String, Any>,
        datum: TicketHistoryModel.Datum,
        Token: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).updatedTicket(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SubmitTicketBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable?) {
                    logEventCustom(
                        e?.localizedMessage ?: "unknown error occurred",
                        null,
                        "v2/ticketUpdate"
                    )
                }

                override fun onNext(saveTicketBean: SubmitTicketBean) {
                    if (datum.isTicketClosed) {
                        val query = HashMap<String, Any>()
                        query["api_token"] = Token
                        datum.tID?.let { query["tId"] = it }
                        closeTicket(query)
                    }
                    if (saveTicketBean.status_code != 200) {
                        logEventCustom(
                            saveTicketBean.message,
                            null,
                            "v2/ticketUpdate"
                        )
                    }
                }
            })
    }

    /*
    Close Ticket Whether created online or offline.
     */
    private fun closeTicket(model: HashMap<String, Any>) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).endTicket(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<EndTicketBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable?) {
                    logEventCustom(
                        e?.localizedMessage ?: "unknown error occurred",
                        null,
                        "v2/ticketClose"
                    )
                }

                override fun onNext(endTicketBean: EndTicketBean) {
                    if (endTicketBean.status_code != 200) {
                        logEventCustom(
                            endTicketBean.message,
                            null,
                            "v2/ticketClose"
                        )
                    }
                }
            })
    }

    /*
    Get list of all tickets which are created offline.
     */
    fun getOfflineCreatedTicket(context: Context) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                try {
                    val list = appDatabase.createOfflineTicketDao().offlineTicketList
                    for (i in list.indices) {
                        val request = list[i].request
                        val gson = Gson().fromJson(request, JsonObject::class.java)
                        val farmerRegistrationBeans = appDatabase.farmerRegistrationDao()
                            .getAllNumberList(gson.get("ticket_no").asString, 0)
                        val farmerList = ArrayList<RegisterFarmerContactModel>()
                        for (farmerRegistrationBean in farmerRegistrationBeans) {
                            val registerFarmerContactModel = RegisterFarmerContactModel()
                            registerFarmerContactModel.phoneNumber =
                                farmerRegistrationBean.mobileNumber
                            registerFarmerContactModel.isDeleted = farmerRegistrationBean.isDeleted
                            registerFarmerContactModel.isSync = farmerRegistrationBean.isSync
                            registerFarmerContactModel.status = farmerRegistrationBean.status
                            registerFarmerContactModel.ticketNumber =
                                farmerRegistrationBean.ticketNumber
                            registerFarmerContactModel.timestamp = farmerRegistrationBean.timeStamp
                            registerFarmerContactModel.type = farmerRegistrationBean.type
                            farmerList.add(registerFarmerContactModel)
                        }
                        appDatabase.ticketHistoryDao()
                            .isFarmerRegistered(gson.get("ticket_no").asString, false)
                        val datum =
                            appDatabase.ticketHistoryDao()
                                .getOfflineTicket(gson.get("ticket_no").asString)
                        createOfflineRequest(context, gson, farmerList, datum, list[i]._id!!)
                    }
                } catch (e: Exception) {
                }
            }
        }
    }

    /*
    Create request of Offline created ticket to send it to server.
     */
    private fun createOfflineRequest(
        context: Context,
        jobj: JsonObject,
        list: ArrayList<RegisterFarmerContactModel>,
        datum: TicketHistoryModel.Datum,
        _id: Int
    ) {
        val query = HashMap<String, Any>()
        query.put("api_token", jobj.get("api_token"))
        query.put("dsId", jobj.get("dsId"))
        query.put("sId", jobj.get("sId"))
        query.put("dId", jobj.get("dId"))
        query.put("tId", jobj.get("tId"))
        query.put("language_id", jobj.get("language_id"))
        query.put("product_1", jobj.get("product_1"))
        query.put("crop_1", jobj.get("crop_1"))
        query.put("ticket_no", jobj.get("ticket_no"))
        if (jobj.has("dd_id")) {
            query.put("dd_id", jobj.get("dd_id"))
        }
        if (jobj.has("territory_id")) {
            query.put("territory_id", jobj.get("territory_id"))
        }
        query.put("product_2", jobj.get("product_2"))
        query.put("crop_2", jobj.get("crop_2"))
        query.put("product_3", jobj.get("product_3"))
        query.put("crop_3", jobj.get("crop_3"))
        query.put("village", datum.village)
        query.put(
            "village_id",
            if (datum.village_id == "") jobj.get("village_id") else datum.village_id
        )
        query.put("expected_farmers", jobj.get("expected_farmers"))
        query.put("expected_pda", jobj.get("expected_pda"))
        query.put("sp_name_1", datum.spName1)
        query.put("sp_number_1", datum.spNumber1)
        query.put("demo_request_id", jobj.get("demo_request_id"))
        query.put("user_farmer", jobj.get("user_farmer"))
        query.put("sp_name_2", datum.spName2)
        query.put("sp_number_2", datum.spNumber2)
        query.put("sp_name_3", datum.spName3)
        query.put("sp_number_3", datum.spNumber3)
        query.put("sp_name_4", datum.spName4)
        query.put("sp_number_4", datum.spNumber4)
        query.put("sp_name_5", datum.spName5)
        query.put("sp_number_5", datum.spNumber5)
        query.put("sp_name_6", datum.spName6)
        query.put("sp_number_6", datum.spNumber6)
        query.put("latitude", jobj.get("latitude"))
        query.put("longitude", jobj.get("longitude"))
        userSubmitTicket(
            context,
            query,
            jobj.get("ticket_no").asString,
            jobj.get("api_token").asString,
            list,
            datum,
            _id
        )
    }

    fun uploadNonUploadedImages(context: Context) {
        val list = DatabaseManager.getNonUploadedImageOfflineTable(context);
        if (list != null) {
            if (list.size > 0) {
                if (list[0].isUploaded != 1 && list[0].isTicketOffline != 1) {
                    val model: ImageOfflineModel = list[0]
                    val imgfile: File?
                    imgfile = File(model.image_url_local)
                    val convert_File_2String = imgfile.toString()
                    val fileNAme =
                        convert_File_2String.substring(convert_File_2String.lastIndexOf("/") + 1)
                    val reqFile = RequestBody.create(MediaType.parse("image/*"), imgfile)
                    val ticke = RequestBody.create(MediaType.parse("text/plain"), model.ticket_id)
                    val token_req = RequestBody.create(
                        MediaType.parse("text/plain"),
                        preference.getString("token")
                    )
                    val lat = RequestBody.create(
                        MediaType.parse("text/plain"),
                        preference.getString(Constants.latitude)
                    )
                    val long = RequestBody.create(
                        MediaType.parse("text/plain"),
                        preference.getString(Constants.longitude)
                    )
                    val date = RequestBody.create(MediaType.parse("text/plain"), model.date)
                    val map: HashMap<String, RequestBody> = HashMap()
                    map.put("photo\"; filename=\"$fileNAme\" ", reqFile)
                    map.put("api_token", token_req)
                    map.put("tId", ticke)
                    map.put("latitude", lat)
                    map.put("longitude", long)
                    map.put("updated_at", date)
                    RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                        .uploadImageToTicketw(map).subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<ImageTicketResponse>() {
                            override fun onCompleted() {}

                            override fun onError(e: Throwable?) {
                                logEventCustom(
                                    e?.localizedMessage ?: "unknown error occurred",
                                    null,
                                    "v2/ticketUpdateImage"
                                )
                            }

                            override fun onNext(editTicketBean: ImageTicketResponse) {
                                editTicketBean.let {
                                    if (it.statusCode == 200 || it.statusCode == 204) {
                                        GlobalScope.launch {
                                            withContext(Dispatchers.IO) {
                                                model.isUploaded = 1;
                                                model.isTicketOffline = 1;
                                                DatabaseManager.updateImageOfflineTable(
                                                    context,
                                                    model
                                                )
                                                DatabaseManager.deleteAllImageOfflineTableParticular(
                                                    context,
                                                    model.ticket_id.toInt()
                                                )
                                            }
                                        }
                                    } else {
                                        logEventCustom(
                                            editTicketBean.message,
                                            null,
                                            "v2/ticketUpdateImage"
                                        )
                                    }
                                }
                            }
                        })
                }
            }
        }

    }

    /*
    Submit Offline created Ticket to the Server.
     */
    private fun userSubmitTicket(
        context: Context,
        model: HashMap<String, Any>,
        ticketNumber: String,
        apiToken: String,
        list: ArrayList<RegisterFarmerContactModel>,
        datum: TicketHistoryModel.Datum,
        _id: Int
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).submitTicket(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SubmitTicketBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable?) {
                    logEventCustom(
                        e?.localizedMessage ?: "unknown error occurred",
                        null,
                        "v2/newTicketSubmit"
                    )
                }

                override fun onNext(submitTicketBean: SubmitTicketBean) {
                    if (submitTicketBean.status_code == 200) {
                        appDatabase.createOfflineTicketDao().deleteItem(_id)
                    }
                    checkIfTicketClosed(submitTicketBean.ticket_id!!, ticketNumber, apiToken)
                    if (datum.isImageUploaded) {
                        val imageModel = DatabaseManager.getNonUploadedImageOfflineTable(
                            context,
                            datum.id.toString()
                        )
                        uploadImage(
                            imageModel,
                            apiToken,
                            submitTicketBean.ticket_id!!,
                            ticketNumber
                        )
                    }
                    if (list.isNotEmpty()) {
                        for (i in list.indices) {
                            list[i].ticketNumber = submitTicketBean.ticket_id
                        }
                        registerFarmerForOfflineTicket(apiToken, list)
                    }
                    if (submitTicketBean.status_code != 200) {
                        logEventCustom(
                            submitTicketBean.message,
                            null,
                            "v2/newTicketSubmit"
                        )
                    }
                }
            })
    }

    private fun uploadImage(
        model: ImageOfflineModel,
        token: String,
        ticketId: String,
        ticketNumber: String
    ) {
        try {
            var imgfile: File? = null
            imgfile = File(model.image_url_local)
            val convert_File_2String = imgfile.toString()
            val fileNAme = convert_File_2String.substring(convert_File_2String.lastIndexOf("/") + 1)
            val reqFile = RequestBody.create(MediaType.parse("image/*"), imgfile)
            val ticke = RequestBody.create(MediaType.parse("text/plain"), ticketId)
            val token_req = RequestBody.create(MediaType.parse("text/plain"), token)
            val lat = RequestBody.create(
                MediaType.parse("text/plain"),
                preference.getString(Constants.latitude)
            )
            val long = RequestBody.create(
                MediaType.parse("text/plain"),
                preference.getString(Constants.longitude)
            )
            val date = RequestBody.create(MediaType.parse("text/plain"), model.date)
            val map: HashMap<String, RequestBody> = HashMap()
            map.put("photo\"; filename=\"$fileNAme\" ", reqFile)
            map.put("api_token", token_req)
            map.put("tId", ticke)
            map.put("latitude", lat)
            map.put("longitude", long)
            map.put("updated_at", date)
            submitgetImageUploadData(ticketNumber, map)
        } catch (ex: Exception) {
        }
    }

    private fun submitgetImageUploadData(
        ticketNumber: String,
        model: HashMap<String, RequestBody>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .uploadImageToTicketw(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ImageTicketResponse>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable?) {
                    logEventCustom(
                        e?.localizedMessage ?: "unknown error occurred",
                        null,
                        "v2/ticketUpdateImage"
                    )
                }

                override fun onNext(editTicketBean: ImageTicketResponse) {
                    editTicketBean.let {
                        if (it.statusCode == 200 || it.statusCode == 204) {
                            GlobalScope.launch {
                                withContext(Dispatchers.IO) {
                                    appDatabase.ticketHistoryDao()
                                        .imageUploaded(ticketNumber, false)
                                }
                            }
                        } else {
                            logEventCustom(
                                editTicketBean.message,
                                null,
                                "v2/ticketUpdateImage"
                            )
                        }
                    }
                }
            })
    }

    /*
    Register the farmer associated with that Ticket that is created Offline..
    */
    private fun registerFarmerForOfflineTicket(
        token: String,
        list: ArrayList<RegisterFarmerContactModel>
    ) {
        val modelData = ContactSyncModel()
        modelData.apiToken = token
        modelData.contactModelList = list
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java)
            .submitNumberOptimized(modelData)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable?) {
                    logEventCustom(
                        e?.localizedMessage ?: "unknown error occurred",
                        null,
                        "v2/farmerRegistrationSubmit"
                    )
                }

                override fun onNext(SubmitFarmersBean: CommonModel) {
                    if (SubmitFarmersBean.status_code != 200) {
                        logEventCustom(
                            SubmitFarmersBean.message,
                            null,
                            "v2/farmerRegistrationSubmit"
                        )
                    }
                }
            })
    }

    /*
    Check is the offline created ticket is closed also in offline mode.
     */
    private fun checkIfTicketClosed(ticketId: String, ticketNumber: String, token: String) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                ticketNumber.replace("\\\"", "")
                val list = appDatabase.ticketHistoryDao().getAllTicketHistory(true)
                for (i in list.indices) {
                    if (list[i].ticket.contains(ticketNumber)) {
                        val data = list[i]
                        if (data.isTicketClosed) {
                            val query = HashMap<String, Any>()
                            query["api_token"] = token
                            query["tId"] = ticketId
                            closeTicket(query)
                            appDatabase.ticketHistoryDao().deleteOfflineData(data)
                            break
                        } else
                            appDatabase.ticketHistoryDao().deleteOfflineData(data)
                    }
                }
            }
        }
    }

    /*
    Get List of planned ticket which are closed offline.
     */
    fun getPlannedClosedTicket(token: String) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val list = appDatabase.atpListDao().getClosedTicketList(true)
                for (i in list.indices) {
                    val query = HashMap<String, Any>()
                    query["apiToken"] = token
                    query["atp_id"] = list[i].plan_atp_id
                    query["reason"] = list[i].reason
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).activateTicketReason(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<ParticipationPlannedResponse>() {
                            override fun onCompleted() {

                            }

                            override fun onError(e: Throwable?) {
                                logEventCustom(
                                    e?.localizedMessage ?: "unknown error occurred",
                                    null,
                                    "v2/activateTicketReason"
                                )
                            }

                            override fun onNext(loginBean: ParticipationPlannedResponse) {
                                if (loginBean.statusCode != 200) {
                                    GlobalScope.launch {
                                        withContext(Dispatchers.Main) {
                                            logEventCustom(
                                                loginBean.message,
                                                null,
                                                "v2/activateTicketReason"
                                            )
                                        }
                                    }
                                }
                                appDatabase.atpListDao()
                                    .updateTicketClosed(list[i].plan_atp_id, false)
                            }
                        })
                }
            }
        }
    }

    /*
    First Activate the tickets which are activated Offline. Then, update or register farmer or close, if required.
     */
    private fun activatePlannedTicket(
        token: String,
        planTicketId: String,
        datum: TicketHistoryModel.Datum
    ) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {

                val query = HashMap<String, Any>()
                query["apiToken"] = token
                query["plan_atp_id"] = planTicketId
                query.put("demo_request_id", "")
                query.put("user_farmer", "")

                RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).activeDemo(query)
                    .subscribeOn(Schedulers.newThread())
                    .observeOn(AndroidSchedulers.mainThread())
                    .subscribe(object : Subscriber<CommonAtpResponse>() {
                        override fun onCompleted() {
                        }

                        override fun onError(e: Throwable?) {
                            logEventCustom(
                                e?.localizedMessage ?: "unknown error occurred",
                                null,
                                "v2/activateAtp"
                            )
                        }

                        override fun onNext(commonBean: CommonAtpResponse) {
                            when {
                                datum.isFarmerRegistered -> {
                                    Single.fromCallable {
                                        val farmerRegistrationBeans =
                                            appDatabase.farmerRegistrationDao()
                                                .getAllNumberList(datum.ticket, 0)
                                        val farmerList = ArrayList<RegisterFarmerContactModel>()
                                        for (farmerRegistrationBean in farmerRegistrationBeans) {
                                            val registerFarmerContactModel =
                                                RegisterFarmerContactModel()
                                            registerFarmerContactModel.phoneNumber =
                                                farmerRegistrationBean.mobileNumber
                                            registerFarmerContactModel.isDeleted =
                                                farmerRegistrationBean.isDeleted
                                            registerFarmerContactModel.isSync =
                                                farmerRegistrationBean.isSync
                                            registerFarmerContactModel.status =
                                                farmerRegistrationBean.status
                                            registerFarmerContactModel.ticketNumber =
                                                farmerRegistrationBean.ticketId
                                            registerFarmerContactModel.timestamp =
                                                farmerRegistrationBean.timeStamp
                                            registerFarmerContactModel.type =
                                                farmerRegistrationBean.type
                                            farmerList.add(registerFarmerContactModel)
                                        }
                                        appDatabase.ticketHistoryDao()
                                            .isFarmerRegistered(datum.ticket, false)
                                        registerFarmer(datum, token, farmerList)
                                    }.subscribeOn(Schedulers.io())
                                        .observeOn(AndroidSchedulers.mainThread())
                                        .subscribe()
                                }
                                datum.isTicketUpdated -> {
                                    Single.fromCallable {
                                        appDatabase.ticketHistoryDao()
                                            .isTicketUpdated(datum.id, false)
                                        updateTicket(datum)
                                    }.subscribeOn(Schedulers.io())
                                        .observeOn(AndroidSchedulers.mainThread())
                                        .subscribe()
                                }
                                datum.isTicketClosed -> {
                                    Single.fromCallable {
                                        appDatabase.ticketHistoryDao().closeTicket(datum.id, false)
                                        val query = HashMap<String, Any>()
                                        query["api_token"] = token
                                        datum.tID?.let { query["tId"] = it }
                                        closeTicket(query)
                                    }.subscribeOn(Schedulers.io())
                                        .observeOn(AndroidSchedulers.mainThread())
                                        .subscribe()
                                }
                            }

                            if (commonBean.status_code != 200) {
                                logEventCustom(
                                    commonBean.message,
                                    null,
                                    "v2/activateAtp"
                                )
                            }
                        }
                    })
            }
        }
    }

    fun inviteFarmer(token: String) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val list = appDatabase.atpFarmerDao().getAllOfflineFarmerList(true)
                for (i in list.indices) {
                    val jArray = JSONArray()
                    val jobj = JSONObject()
                    jobj.accumulate("farmer_name", list[i].farmerName)
                    jobj.accumulate("contact_number", list[i].contactNumber)
                    jArray.put(jobj)
                    val model: String = token
                    val query = HashMap<String, Any>()
                    query["apiToken"] = model
                    query["plan_atp_id"] = list[i].planAtpId
                    query["farmerData"] = jArray.toString()

                    RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                        .getInviteFarmer(query).subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<CommonAtpResponse>() {
                            override fun onCompleted() {
                            }

                            override fun onError(e: Throwable?) {
                                logEventCustom(
                                    e?.localizedMessage ?: "unknown error occurred",
                                    null,
                                    "v2/inviteFarmer"
                                )
                            }

                            override fun onNext(commonBean: CommonAtpResponse) {
                                if (commonBean.status_code != 200) {
                                    logEventCustom(
                                        commonBean.message,
                                        null,
                                        "v2/inviteFarmer"
                                    )
                                }
                                Single.fromCallable {
                                    appDatabase.atpFarmerDao()
                                        .updateStatus(list[i].planAtpId, false)
                                }.subscribeOn(Schedulers.io())
                                    .observeOn(AndroidSchedulers.mainThread())
                                    .subscribe()
                            }
                        })
                }
            }
        }
    }

    // Upload All the Farmers which are created from My Villages Section
    fun getOfflineFarmer() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val list = appDatabase.addFarmerDao().allFarmerList
                for (i in list.indices) {
                    uploadFarmer(list[i])
                }
            }
        }
    }

    private fun uploadFarmer(addFarmerModel: AddFarmerModel) {
        val map = HashMap<String, RequestBody>()
        map["ECN"] = getRequestBody(addFarmerModel.ECN!!)
        map["apiToken"] = getRequestBody(addFarmerModel.apiToken!!)
        map["villageId"] = getRequestBody(addFarmerModel.villageId!!)

        map["farmerName"] = getRequestBody(addFarmerModel.farmerName!!)
        map["fatherName"] = getRequestBody(addFarmerModel.fatherName!!)
        map["contactnumber"] = getRequestBody(addFarmerModel.contactnumber!!)
        map["alternateContactnumber"] = getRequestBody(addFarmerModel.alternateContactnumber!!)

        map["cropId"] = getRequestBody(addFarmerModel.cropId!!)
        map["acres"] = getRequestBody(addFarmerModel.acres!!)

        map["farmerLat"] = getRequestBody(addFarmerModel.farmerLat!!)
        map["farmerLong"] = getRequestBody(addFarmerModel.farmerLong!!)

        val file = File(addFarmerModel.filePath!!)
        val fileBody1 = RequestBody.create(MediaType.parse("image/*"), file)
        val imagePart1: MultipartBody.Part =
            MultipartBody.Part.createFormData("farmerImage", addFarmerModel.fileName, fileBody1)

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).addFarmer(map, imagePart1)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<AddFarmerSuccessModel>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable?) {
                    logEventCustom(
                        e?.localizedMessage ?: "unknown error occurred",
                        null,
                        "v2/addFarmer"
                    )
                }

                override fun onNext(loginBean: AddFarmerSuccessModel) {
                    if (loginBean.status_code != 200) {
                        logEventCustom(
                            loginBean.message,
                            null,
                            "v2/addFarmer"
                        )
                    }
                    deleteAddFarmerModel(addFarmerModel._id!!)
                }
            })
    }

    private fun deleteAddFarmerModel(id: Int) {
        Single.fromCallable {
            appDatabase.addFarmerDao().deleteTableData(id)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getRequestBody(value: String): RequestBody {
        return RequestBody.create(MediaType.parse("application/json; charset=utf-8"), value)
    }

    fun getOfflineDemoRequest() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val list = appDatabase.demoRequestDao().allDemoRequestList
                for (i in list.indices) {
                    uploadDemoRequest(list[i])
                }
            }
        }
    }

    private fun uploadDemoRequest(it: DemoRequestOfflineModel) {
        val query = HashMap<String, RequestBody>()
        query.put("apiToken", getRequestBody(it.apiToken!!))
        query.put("cropId", getRequestBody(it.cropId!!))
        query.put("productId", getRequestBody(it.productId!!))
        query.put("farmerName", getRequestBody(it.farmerName!!))
        query.put("stateId", getRequestBody(it.stateId!!))
        query.put("districtId", getRequestBody(it.districtId!!))
        query.put("tehsilId", getRequestBody(it.tehsilId!!))
        query.put("pincode", getRequestBody(it.pincode!!))
        query.put("villageId", getRequestBody(it.villageId!!))
        query.put("contactNumber", getRequestBody(it.contactNumber!!))
        query.put("showingDate", getRequestBody(it.showingDate!!))
        query.put("spray", getRequestBody(it.spray!!))
        query.put("latitude", getRequestBody(it.latitude!!.toString()))
        query.put("longitude", getRequestBody(it.longitude!!.toString()))
        query.put("farmer_image", getRequestBody(""))
        query.put("farmer_type", getRequestBody(it.farmer_type!!))

        if (it.farmer_image!!.isNotEmpty()) {
            val file = File(it.farmer_image)
            val fileBody1 = RequestBody.create(MediaType.parse("image/*"), file)
            val imagePart1: MultipartBody.Part =
                MultipartBody.Part.createFormData("demoImage", it.fileName, fileBody1)

            RetrofitClient.mvpClient
                .create(ApiServiceRequest::class.java).createRequestDemo(query, imagePart1)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<CreateRequestDemoResponse>() {
                    override fun onCompleted() {
                    }

                    override fun onError(e: Throwable?) {
                        logEventCustom(
                            e?.localizedMessage ?: "unknown error occurred",
                            null,
                            "v2/createDemoRequest"
                        )
                    }

                    override fun onNext(villageBean: CreateRequestDemoResponse) {
                        if (villageBean.status_code != 200) {
                            logEventCustom(
                                villageBean.message,
                                null,
                                "v2/createDemoRequest"
                            )
                        }
                        deleteDemoRequestModel(it._id!!)
                    }
                })
        } else {
            val fileBody1 = RequestBody.create(MediaType.parse("image/*"), "")
            val imagePart1: MultipartBody.Part =
                MultipartBody.Part.createFormData("demoImage", "", fileBody1)

            RetrofitClient.mvpClient
                .create(ApiServiceRequest::class.java).createRequestDemo(query, imagePart1)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<CreateRequestDemoResponse>() {
                    override fun onCompleted() {
                    }

                    override fun onError(e: Throwable?) {
                        logEventCustom(
                            e?.localizedMessage ?: "unknown error occurred",
                            null,
                            "v2/createDemoRequest2"
                        )
                    }

                    override fun onNext(villageBean: CreateRequestDemoResponse) {
                        if (villageBean.status_code != 200) {
                            logEventCustom(
                                villageBean.message,
                                null,
                                "v2/createDemoRequest2"
                            )
                        }
                        deleteDemoRequestModel(it._id!!)
                    }
                })
        }
    }

    private fun deleteDemoRequestModel(id: Int) {
        Single.fromCallable {
            appDatabase.demoRequestDao().deleteTableData(id)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
        }.subscribe()
    }

    fun getMarkVisitList() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                delay(2000)
                val data: MarkVisitRequestModel? =
                    appDatabase.markVisitRequestDao()?.getSingleMarkVisitData(false)
                data?.let {
                    if (it != null) {
                        callApiUploadData(it)
                    }
                }
            }
        }
    }

    fun resetMarkVisit() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val data: List<MarkVisitRequestModel?>? =
                    appDatabase.markVisitRequestDao()?.getAllMarkVisitData()
                data?.let {
                    it.forEach { it1 ->
                        appDatabase.markVisitRequestDao()?.updateMarkVisitUploaded(false, it1?.id)
                    }
                }
                delay(200)
            }.run {
                withContext(Dispatchers.Main) {
                    getMarkVisitList()
                }
            }
        }
    }

    private fun callApiUploadData(markVisitRequestModel: MarkVisitRequestModel) {
        val map = HashMap<String, RequestBody>()
        map["apiToken"] = getRequestBody(preference.getString("token"))
        map["villageId"] = getRequestBody(markVisitRequestModel.villageId)
        map["farmerId"] = getRequestBody(markVisitRequestModel.farmerId)
        map["cropId"] = getRequestBody(markVisitRequestModel.cropId)

        val fileBody1 = RequestBody.create(
            MediaType.parse("image/*"),
            File(markVisitRequestModel.imgFile)
        )

        val imagePart1: MultipartBody.Part =
            MultipartBody.Part.createFormData(
                "visitImage",
                markVisitRequestModel.imgName,
                fileBody1
            )
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.markVisitRequestDao()
                    ?.updateMarkVisitUploaded(true, markVisitRequestModel.id)
            }
        }
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).markVisit(map, imagePart1)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(
                object : Subscriber<MarkVisitBean>() {

                    override fun onCompleted() {
                    }

                    override fun onError(e: Throwable?) {
                        GlobalScope.launch {
                            withContext(Dispatchers.IO) {
                                val data: MarkVisitRequestModel? =
                                    appDatabase.markVisitRequestDao()
                                        ?.getSingleMarkVisitData(false)
                                appDatabase.markVisitRequestDao()?.updateMarkVisitUploaded(
                                    false, markVisitRequestModel.id
                                )
                                data?.let {
                                    if (it != null)
                                        callApiUploadData(it)
                                }
                            }
                        }
                        logEventCustom(
                            e?.localizedMessage ?: "unknown error occurred",
                            null,
                            "v2/markVisit"
                        )
                    }

                    override fun onNext(villageBean: MarkVisitBean) {
                        if (villageBean.statusCode == 200) {
                            GlobalScope.launch {
                                withContext(Dispatchers.IO) {
                                    appDatabase.markVisitRequestDao()
                                        .deleteTableData(markVisitRequestModel.id)
                                    val data: MarkVisitRequestModel? =
                                        appDatabase.markVisitRequestDao()
                                            ?.getSingleMarkVisitData(false)
                                    data?.let {
                                        if (it != null)
                                            callApiUploadData(it)
                                    }
                                }
                            }
                        } else {
                            GlobalScope.launch {
                                withContext(Dispatchers.IO) {
                                    val data: MarkVisitRequestModel? =
                                        appDatabase.markVisitRequestDao()
                                            ?.getSingleMarkVisitData(false)
                                    appDatabase.markVisitRequestDao()?.updateMarkVisitUploaded(
                                        false, markVisitRequestModel.id
                                    )
                                    data?.let {
                                        if (it != null)
                                            callApiUploadData(it)
                                    }
                                }
                            }
                            logEventCustom(
                                villageBean.message,
                                null,
                                "v2/markVisit"
                            )
                        }
                    }
                },
            )
    }

    fun hardRefreshIfc(){
        GlobalScope.launch {
            withContext(Dispatchers.IO){
                val allDataNotUploaded = appDatabase.ifcFarmerDao()
                    ?.getAllIfcFarmerDataForTicketNotUploaded(false)
                allDataNotUploaded?.forEach { it ->
                    if (it != null) {
                        appDatabase.ifcFarmerDao()
                            ?.updateIfcFarmerUploadedData(0, it.ticket_no, it.mobile)
                    }
                }
                delay(2000)
                getIfcFarmerData();
            }
        }
    }

    fun getIfcFarmerData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                delay(2000)
                val data = appDatabase.ifcFarmerDao()?.getSingleIfcFarmerData(
                    status = 0,
                    isuploaded = false
                )
                if (data != null) {
                    getUniqueActivityDuration(data.ticket_no)
                    callApiForFarmerUpload(data, 1)
                } else {
                    val data2 = appDatabase.ifcFarmerDao()?.getSingleIfcFarmerData(
                        status = 2,
                        isuploaded = false
                    )
                    if (data2 != null) {
                        getUniqueActivityDuration(data2.ticket_no)
                        callApiForFarmerUpload(data2, 2)
                    }
                }
            }
        }
    }

    private fun callApiForFarmerUpload(data: AddIfcFarmer, value: Int) {
        if (value == 1) {
            appDatabase.ifcFarmerDao()?.updateIfcFarmerUploadedData(2, data.ticket_no, data.mobile)
        } else if (value == 2) {
            appDatabase.ifcFarmerDao()?.updateIfcFarmerUploadedData(3, data.ticket_no, data.mobile)
        }

        val fileBody1 = RequestBody.create(MediaType.parse("image/*"), File(data.image))
        val imagePart1: MultipartBody.Part =
            MultipartBody.Part.createFormData("image", data.imageName, fileBody1)
        Log.e(TAG, "callApiForFarmerUpload: ${System.currentTimeMillis()}")
        val map = HashMap<String, RequestBody>()
        map["apiToken"] = getRequestBody(preference.getString("token"))
        map["farmer_name"] = getRequestBody(data.farmer_name!!)
        map["mobile"] = getRequestBody(data.mobile!!)
        map["image_date_time"] = getRequestBody(data.serverTime!!)
        map["latitude"] = getRequestBody(data.latitude.toString())
        map["longitude"] = getRequestBody(data.longitude.toString())
        map["acreage"] = getRequestBody(data.acrers.toString())
        map["ticket_no"] = getRequestBody(data.ticket_no.toString())
        map["activity_distance"] = getRequestBody(data.getActivityDistance().toString())

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).addIfcFarmer(map, imagePart1)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CreateRequestDemoResponse>() {

                override fun onCompleted() {}

                override fun onError(e: Throwable?) {
                    logEventCustom(
                        e?.localizedMessage ?: "unknown error occurred",
                        null,
                        "v3/addIfcFarmer"
                    )
                    logEventAddIfcFarmers("Server Error : ", data)
                }

                override fun onNext(villageBean: CreateRequestDemoResponse?) {
                    villageBean.let {
                        if (it != null) {
                            if (it.status_code == 200) {
                                deleteDemoRequestModel(
                                    data.ticket_no!!,
                                    data.mobile!!,
                                    true,
                                    value,
                                    data.serverId
                                )
                            } else {
                                logEventCustom(
                                    it.message,
                                    null,
                                    "v3/addIfcFarmer"
                                )
                                logEventAddIfcFarmers("Api Error : ${it.message}", data)
                                deleteDemoRequestModel(
                                    data.ticket_no!!,
                                    data.mobile!!,
                                    false,
                                    value,
                                    data.serverId
                                )
                            }
                        } else {
                            logEventCustom(
                                it?.message ?: "Custom Message -> Other error occurred",
                                null,
                                "v3/addIfcFarmer"
                            )
                            logEventAddIfcFarmers("Api Error : Response null ", data)
                            deleteDemoRequestModel(
                                data.ticket_no!!,
                                data.mobile!!,
                                false,
                                value,
                                data.serverId
                            )
                        }
                    }
                }
            })
    }

    private fun logEventAddIfcFarmers(s: String, ticketNumber: AddIfcFarmer) {
        val model = LogModel()
        val data =
            "Ticket No : ${ticketNumber.ticket_no} | Ifc Farmers Api Data -> ${
                Gson().toJson(ticketNumber)
            }".trimIndent()
        model.apiData = data
        model.apiName =
            "Api Name : v3/addIfcFarmer | Message ${if (TextUtils.isEmpty(s)) "Api Called" else s}"
        model.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
        val userData = (preference.getString("user_id") + "_"
                + preference.getString("ecn") + "_"
                + preference.getString("user_type") + "_"
                + preference.getString("Username") + "_"
                + preference.getString("designation"))
        model.userData = "\nUSER DATA : -> $userData"
        AppLogFile.writeLineInFile(Gson().toJson(model), preference.getString("ecn"))
    }

    fun deleteDemoRequestModel(
        ticketNo: String,
        mobile: String,
        isUploaded: Boolean,
        from: Int,
        id: Int
    ) {
        Single.fromCallable {
            if (isUploaded && from == 1) {
                appDatabase.ifcFarmerDao()?.deleteTableData(id)
            } else if (isUploaded && from == 2) {
                appDatabase.ifcFarmerDao()?.deleteTableData(id)
            }
            if (from == 1) {
                val data = appDatabase.ifcFarmerDao()?.getSingleIfcFarmerData(
                    status = 0,
                    isuploaded = false
                )
                if (data != null) {
                    callApiForFarmerUpload(data, 1)
                }
            }
            if (from == 2) {
                val data2 = appDatabase.ifcFarmerDao()?.getSingleIfcFarmerData(
                    status = 2,
                    isuploaded = false
                )
                if (data2 != null) {
                    callApiForFarmerUpload(data2, 2)
                }
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
        }.subscribe()
    }

    fun getUniqueActivityDuration(ticketNo: String) {
        Single.fromCallable {
            val list = appDatabase.activityDurationDao()?.getAllActivityDurationData(
                status = 0,
                ticketNumber = ticketNo
            ) as ArrayList<ActivityDurationModel>
            if (!list.isNullOrEmpty()) {
                callApiUpdateActivity(list)
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
        }.subscribe()
    }

    private fun callApiUpdateActivity(list: ArrayList<ActivityDurationModel>) {
        list.forEach {
            val query = HashMap<String, Any>()
            query["token"] = preference.getString("token")
            query["ticket_no"] = it.ticket_no
            query["activity_duration"] = it.activity_duration.toString()
            query["total_activity_distance"] = it.total_activity_distance.toString()
            query["total_pda"] = it.total_pda
            RetrofitClient.mvpClient
                .create(ApiServiceRequest::class.java).updateActivityDuration(query)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<CreateRequestDemoResponse>() {
                    override fun onCompleted() {
                    }

                    override fun onError(e: Throwable?) {
                        logEventCustom(
                            e?.localizedMessage ?: "unknown error occurred",
                            null,
                            "v3/updateifcactivity"
                        )
                    }

                    override fun onNext(data: CreateRequestDemoResponse?) {
                        data?.let { v ->
                            deleteActivityFromDataBase(it)
                            if (v.status_code != 200) {
                                logEventCustom(
                                    v.message,
                                    null,
                                    "v3/updateifcactivity"
                                )
                            }
                        }
                    }
                })
        }
    }

    private fun deleteActivityFromDataBase(activityDurationModel: ActivityDurationModel) {
        Single.fromCallable {
//            appDatabase.activityDurationDao()?.deleteTableData(
//                activityDurationModel.id
//            )
            appDatabase.activityDurationDao()?.deleteTableData(activityDurationModel.ticket_no)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
        }.subscribe()
    }

    //for Manual ticket data offline
    fun uploadManualTicketData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (24 * 60 * 60 * 1000)
                val data: ManualTicketClaimImage? =
                    appDatabase.manualTicketClaimDao()
                        .getSosManualTicketClaimData(currentHitTime)
                if (data != null) {
                    val listOfDbData =
                        appDatabase.manualTicketClaimDao().getAllManualTicketClaimData()
                    listOfDbData?.forEach {
                        val currentHitTimeTwoHour = System.currentTimeMillis()
                        appDatabase.manualTicketClaimDao().updateManualTicketClaimData(
                            it.id,
                            0,
                            it.ticket_number,
                            currentHitTimeTwoHour
                        )
                    }
                }
            }
            delay(1000L)
            withContext(Dispatchers.Main) {
                getManualTicketData()
            }
        }
    }

    fun getManualTicketData() {
        val token = preference.getString("token")
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis()
                val data: ManualTicketClaimImage? =
                    appDatabase.manualTicketClaimDao()
                        .getSingleManualTicketClaimData(0, currentHitTime, 1)
                data?.let {
                    uploadTicketDataManual(data, token)
                }
            }
        }
    }

    private fun uploadTicketDataManual(
        model: ManualTicketClaimImage,
        token: String
    ) {
        try {
            var imgfile: File? = null
            imgfile = File(model.image_url_local)
            val convertFile2String = imgfile.toString()
            val fileNAme = convertFile2String.substring(convertFile2String.lastIndexOf("/") + 1)
            val reqFile = RequestBody.create(MediaType.parse("image/*"), imgfile)
            val ticketNo = RequestBody.create(MediaType.parse("text/plain"), model.ticket_number)
            val tokenReq = RequestBody.create(MediaType.parse("text/plain"), token)
            val lat = RequestBody.create(
                MediaType.parse("text/plain"),
                preference.getString(Constants.latitude)
            )
            val long = RequestBody.create(
                MediaType.parse("text/plain"),
                preference.getString(Constants.longitude)
            )
            val date = RequestBody.create(MediaType.parse("text/plain"), model.date)
            val map: HashMap<String, RequestBody> = HashMap()
            map["photo\"; filename=\"$fileNAme\" "] = reqFile
            map["api_token"] = tokenReq
            map["ticket_no"] = ticketNo
            map["latitude"] = lat
            map["longitude"] = long
            map["updated_at"] = date

            appDatabase.manualTicketClaimDao()
                .updateManualTicketClaimData(model.id, 1, model.ticket_number, model.type)
            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                .uploadImageToTicketByNumber(map).subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<CommonAtpResponse>() {
                    override fun onCompleted() {}

                    override fun onError(e: Throwable?) {
                        GlobalScope.launch {
                            withContext(Dispatchers.IO) {
                                val currentHitTimeTwoHour =
                                    System.currentTimeMillis() + (121 * 60 * 1000)
                                appDatabase.manualTicketClaimDao()
                                    .updateManualTicketClaimData(
                                        model.id,
                                        0,
                                        model.ticket_number,
                                        currentHitTimeTwoHour
                                    )
                            }.run {
                                withContext(Dispatchers.Main) {
                                    getManualTicketData()
                                }
                            }
                        }
                        logEventCustom(
                            e?.localizedMessage ?: "unknown error occurred",
                            null,
                            "v3/ticketUpdateImageBytktno"
                        )
                    }

                    override fun onNext(editTicketBean: CommonAtpResponse) {
                        GlobalScope.launch {
                            if (editTicketBean.status_code == 200) {
                                withContext(Dispatchers.IO) {
                                    val data: ManualTicketClaimImage? =
                                        appDatabase.manualTicketClaimDao()
                                            .getSingleManualTicketClaimData(1)
                                    data?.let {
                                        appDatabase.manualTicketClaimDao()
                                            .deleteManualTicketClaimData(
                                                it.id
                                            )
                                    }
                                }.run {
                                    withContext(Dispatchers.Main) {
                                        getManualTicketData()
                                    }
                                }
                            } else {
                                withContext(Dispatchers.IO) {
                                    val currentHitTimeTwoHour =
                                        System.currentTimeMillis() + (121 * 60 * 1000)
                                    appDatabase.manualTicketClaimDao()
                                        .updateManualTicketClaimData(
                                            model.id,
                                            0,
                                            model.ticket_number,
                                            currentHitTimeTwoHour
                                        )
                                }.run {
                                    withContext(Dispatchers.Main) {
                                        getManualTicketData()
                                        logEventCustom(
                                            editTicketBean.message,
                                            null,
                                            "v3/ticketUpdateImageBytktno"
                                        )
                                    }
                                }
                            }

                        }
                    }
                })
        } catch (ex: Exception) {
            ex.printStackTrace()
        }
    }

    //reset offline arrival departure
    fun resetManualArrivalDepartureData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (15 * 60 * 1000)
                val listOfDbData =
                    appDatabase.manualTicketClaimDao().getAllManualTicketClaimData()
                listOfDbData?.forEach {
                    appDatabase.manualTicketClaimDao().updateManualTicketClaimData(
                        it.id,
                        0,
                        it.ticket_number,
                        currentHitTime
                    )
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    getManualSelfParticipation()
                }
            }
        }
    }

    //for Arrival departure offline
    fun uploadManualSelfParticipationTicketData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (24 * 60 * 60 * 1000)
                val data: ManualTicketClaimImage? =
                    appDatabase.manualTicketClaimDao().getSosManualTicketClaimData(currentHitTime)
                if (data != null) {
                    val listOfDbData =
                        appDatabase.manualTicketClaimDao().getAllManualTicketClaimData()
                    listOfDbData?.forEach {
                        val currentHitTimeTwoHour = System.currentTimeMillis()
                        appDatabase.manualTicketClaimDao().updateManualTicketClaimData(
                            it.id,
                            0,
                            it.ticket_number,
                            currentHitTimeTwoHour
                        )
                    }
                }
            }
            delay(1000L)
            withContext(Dispatchers.Main) {
                getManualSelfParticipation()
            }
        }
    }

    private fun getManualSelfParticipation() {
        val token = preference.getString("token")
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis()
                val data: ManualTicketClaimImage? =
                    appDatabase.manualTicketClaimDao()
                        .getSingleManualTicketClaimData(0, currentHitTime, 3)
                data?.let {
                    uploadManualSelfParticipation(data, token)
                }
            }
        }
    }

    private fun uploadManualSelfParticipation(
        data: ManualTicketClaimImage,
        token: String
    ) {

        val map: MutableMap<String, RequestBody> = HashMap()

        map["apiToken"] = RequestBody.create(MediaType.parse("text/plain"), token)
        map["tId"] = RequestBody.create(MediaType.parse("text/plain"), data.ticket_number)
        map["is_online"] = RequestBody.create(MediaType.parse("text/plain"), "0")
        map["arival_desc"] = RequestBody.create(MediaType.parse("text/plain"), data.description)
        map["arival_time"] = RequestBody.create(MediaType.parse("text/plain"), data.date)
        map["arival_latitude"] = RequestBody.create(MediaType.parse("text/plain"), data.latitude)
        map["arival_longitude"] = RequestBody.create(MediaType.parse("text/plain"), data.longitude)
        map["departure_time"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_time)
        map["departure_desc"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_comment)
        map["departure_latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_latitude)
        map["departure_longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_longitude)
        map["duration"] = RequestBody.create(MediaType.parse("text/plain"), data.duration)

        val parts: ArrayList<MultipartBody.Part> = ArrayList()
        val arrivalFile = File(data.image_url_local)
        val departureFile = File(data.departure_imageURL)
        try {
            val requestFile1: RequestBody =
                RequestBody.create(MediaType.parse("image/*"), arrivalFile)
            val part =
                MultipartBody.Part.createFormData("arival_photo", arrivalFile.name, requestFile1)
            parts.add(part)

            val requestFile2: RequestBody =
                RequestBody.create(MediaType.parse("image/*"), departureFile)
            val part2 =
                MultipartBody.Part.createFormData(
                    "departure_photo",
                    departureFile.name,
                    requestFile2
                )
            parts.add(part2)
        } catch (e: JSONException) {
            e.printStackTrace()
        }
        appDatabase.manualTicketClaimDao().updateManualTicketClaimData(data.id, 1)
        val okHttpClient = RetrofitClient.httpClient
        val mGson = GsonBuilder().excludeFieldsWithoutExposeAnnotation().create()
        val builder: Retrofit.Builder = Retrofit.Builder()
            .baseUrl(RetrofitClient.LIVE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create(mGson))
        val retrofit = builder.build()
        val client: ApiServiceRequest = retrofit.create(
            ApiServiceRequest::class.java
        )
        val call: Call<ArrivalDepartureResponse> = client.atpFeedBackNewFile(map, parts)
        call.enqueue(object : Callback<ArrivalDepartureResponse?> {
            override fun onResponse(
                call: Call<ArrivalDepartureResponse?>,
                response: Response<ArrivalDepartureResponse?>
            ) {
                GlobalScope.launch {
                    response.body()?.let {
                        if (it.status_code == 200) {
                            withContext(Dispatchers.IO) {
                                val dataValue: ManualTicketClaimImage? =
                                    appDatabase.manualTicketClaimDao()
                                        .getSingleManualTicketClaimData(1)
                                dataValue?.let { its ->
                                    appDatabase.manualTicketClaimDao()
                                        .deleteManualTicketClaimData(
                                            its.id
                                        )
                                }
                            }.run {
                                delay(1000L)
                                withContext(Dispatchers.Main) {
                                    getManualSelfParticipation()
                                }
                            }
                        } else {
                            withContext(Dispatchers.IO) {
                                val currentHitTimeTwoHour =
                                    System.currentTimeMillis() + (121 * 60 * 1000)
                                appDatabase.manualTicketClaimDao()
                                    .updateManualTicketClaimData(
                                        data.id,
                                        0,
                                        data.ticket_number,
                                        currentHitTimeTwoHour
                                    )
                            }.run {
                                delay(1000L)
                                withContext(Dispatchers.Main) {
                                    getManualSelfParticipation()
                                    logEventCustom(
                                        it.message,
                                        null,
                                        "v2/selfParticipationArivalDepartureSubmit"
                                    )
                                }
                            }
                        }
                    }

                }
            }

            override fun onFailure(call: Call<ArrivalDepartureResponse?>, t: Throwable) {
                GlobalScope.launch {
                    withContext(Dispatchers.IO) {
                        val currentHitTimeTwoHour = System.currentTimeMillis() + (121 * 60 * 1000)
                        appDatabase.manualTicketClaimDao()
                            .updateManualTicketClaimData(
                                data.id,
                                0,
                                data.ticket_number,
                                currentHitTimeTwoHour
                            )
                    }
                    withContext(Dispatchers.Main) {
                        getManualSelfParticipation()
                        logEventCustom(
                            t.localizedMessage ?: "unknown error occured",
                            null,
                            "v2/selfParticipationArivalDepartureSubmit"
                        )
                    }
                }
            }
        })
    }

    //reset online arrival departure
    fun resetArrivalDepartureData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (15 * 60 * 1000)
                val listOfDbData =
                    appDatabase.arrivalDepartureParticipationDao().getAllArrivalDepartureData()
                listOfDbData?.forEach {
                    appDatabase.arrivalDepartureParticipationDao().updateArrivalDeparture(
                        it.id,
                        currentHitTime,
                        0,
                        it.ticket_no,
                    )
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    uploadServiceArrivalDeparture()
                }
            }
        }
    }

    //for Arrival departure online
    fun uploadArrivalDepartureData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (24 * 60 * 60 * 1000)
                val data: ArrivalDepartureModelOffline? =
                    appDatabase.arrivalDepartureParticipationDao()
                        .getSosArrivalDepartureData(currentHitTime)
                if (data != null) {
                    val listOfDbData =
                        appDatabase.arrivalDepartureParticipationDao()
                            .getAllArrivalDepartureData()
                    listOfDbData?.forEach {
                        val currentHitTimeTwoHour = System.currentTimeMillis()
                        appDatabase.arrivalDepartureParticipationDao().updateArrivalDeparture(
                            it.id,
                            currentHitTimeTwoHour,
                            0,
                            it.ticket_no,
                        )
                    }
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    uploadServiceArrivalDeparture()
                }
            }
        }
    }

    private fun uploadServiceArrivalDeparture() {
        val token = preference.getString("token")
        GlobalScope.launch {
            var data: ArrivalDepartureModelOffline? = null
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis()
                data = appDatabase.arrivalDepartureParticipationDao()
                    .getSingleArrivalDepartureData(0, currentHitTime, 3)
            }.run {
                withContext(Dispatchers.Main) {
                    data?.let {
                        multipleFileUploadWebservice(it, token)
                    }
                }
            }
        }
    }

    private fun multipleFileUploadWebservice(
        data: ArrivalDepartureModelOffline,
        token: String
    ) {
        val map: MutableMap<String, RequestBody> = HashMap()
        map["apiToken"] = RequestBody.create(MediaType.parse("text/plain"), token)
        map["tId"] = RequestBody.create(
            MediaType.parse("text/plain"),
            if (data.ticket_id == null) data.ticket_no else data.ticket_id
        )
        map["is_online"] = RequestBody.create(MediaType.parse("text/plain"), "1")
        map["arival_desc"] =
            RequestBody.create(MediaType.parse("text/plain"), data.comment ?: "")
        map["arival_time"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_time)
        map["arival_latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.latitude)
        map["arival_longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.longitude)
        map["departure_time"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_time)
        map["departure_desc"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_comment)
        map["departure_latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_latitude)
        map["departure_longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_longitude)
        map["duration"] = RequestBody.create(MediaType.parse("text/plain"), data.duration)

        val parts: ArrayList<MultipartBody.Part> = ArrayList()
        val arrivalFile = File(data.imageURL)
        val departureFile = File(data.departure_imageURL)
        try {
            val requestFile1: RequestBody =
                RequestBody.create(MediaType.parse("image/*"), arrivalFile)
            val part =
                MultipartBody.Part.createFormData(
                    "arival_photo",
                    arrivalFile.name,
                    requestFile1
                )
            if (File(data.imageURL).length() > 0) {
                parts.add(part)
            }

            val requestFile2: RequestBody =
                RequestBody.create(MediaType.parse("image/*"), departureFile)
            val part2 =
                MultipartBody.Part.createFormData(
                    "departure_photo",
                    departureFile.name,
                    requestFile2
                )
            isFileLessThan2KB(arrivalFile);
            if (File(data.departure_imageURL).length() > 0) {
                parts.add(part2)
            }
        } catch (e: JSONException) {
            e.printStackTrace()
        }

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.arrivalDepartureParticipationDao()
                    .updateArrivalDepartureData(data.id, 1)
            }
        }

        val okHttpClient = RetrofitClient.httpClient
        val builder: Retrofit.Builder = Retrofit.Builder()
            .baseUrl(RetrofitClient.LIVE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
        val retrofit = builder.build()
        val client: ApiServiceRequest = retrofit.create(
            ApiServiceRequest::class.java
        )
        val call: Call<ArrivalDepartureResponse> = client.atpFeedBackNewFile(map, parts)
        call.enqueue(object : Callback<ArrivalDepartureResponse?> {
            override fun onResponse(
                call: Call<ArrivalDepartureResponse?>,
                response: Response<ArrivalDepartureResponse?>
            ) {
                if (response.body() != null) {
                    val model: ArrivalDepartureResponse? = response.body()
                    if (model != null) {
                        GlobalScope.launch {
                            if (model.status_code == 200) {
                                withContext(Dispatchers.IO) {
                                    val dataGot: ArrivalDepartureModelOffline? =
                                        appDatabase.arrivalDepartureParticipationDao()
                                            .getSingleArrivalDepartureData(1)
                                    dataGot?.let {
                                        appDatabase.arrivalDepartureParticipationDao()
                                            .deleteArrivalDepartureData(
                                                it.id
                                            )
                                    }
                                }.run {
                                    withContext(Dispatchers.Main) {
                                        uploadServiceArrivalDeparture()
                                    }
                                }
                            } else {
                                withContext(Dispatchers.IO) {
                                    val currentHitTimeTwoHour =
                                        System.currentTimeMillis() + (121 * 60 * 1000)
                                    appDatabase.arrivalDepartureParticipationDao()
                                        .updateArrivalDeparture(
                                            data.id,
                                            currentHitTimeTwoHour,
                                            0,
                                            data.ticket_no
                                        )
                                }.run {
                                    delay(1000)
                                    withContext(Dispatchers.Main) {
                                        uploadServiceArrivalDeparture()
                                        logEventCustom(
                                            model.message ?: "unknown error occured",
                                            null,
                                            "v3/selfParticipationArivalDepartureSubmit"
                                        )
                                    }
                                }
                            }
                        }
                    }
                }
            }

            override fun onFailure(call: Call<ArrivalDepartureResponse?>, t: Throwable) {
                GlobalScope.launch {
                    withContext(Dispatchers.Main) {
                        uploadServiceArrivalDeparture()
                        logEventCustom(
                            t.localizedMessage ?: "unknown error occured",
                            null,
                            "v3/selfParticipationArivalDepartureSubmit"
                        )
                    }.run {
                        withContext(Dispatchers.IO) {
                            val currentHitTimeTwoHour =
                                System.currentTimeMillis() + (121 * 60 * 1000)
                            appDatabase.arrivalDepartureParticipationDao()
                                .updateArrivalDeparture(
                                    data.id,
                                    currentHitTimeTwoHour,
                                    0,
                                    data.ticket_no
                                )
                        }
                    }

                }
            }
        })
    }

    fun uploadRequestMaterialData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (24 * 60 * 60 * 1000)
                val data: RequestMaterialModel? =
                    appDatabase.requestMaterialModelDao()
                        .getSosMaterialRequestData(currentHitTime)
                if (data != null) {
                    val listOfDbData =
                        appDatabase.requestMaterialModelDao().getAllMaterialRequestData()
                    listOfDbData?.forEach {
                        val currentHitTimeTwoHour = System.currentTimeMillis()
                        appDatabase.requestMaterialModelDao().updateRequestMaterialData(
                            it?.id!!,
                            currentHitTimeTwoHour,
                            false,
                        )
                    }
                }
            }
            delay(600L)
            withContext(Dispatchers.Main) {
                uploadRequestMaterial()
            }
        }
    }

    private fun uploadRequestMaterial() {
        val token = preference.getString("token")
        GlobalScope.launch {
            var data: RequestMaterialModel? = null
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis()
                data = appDatabase.requestMaterialModelDao()
                    .getSingleRequestMaterial(false, currentHitTime)
            }.run {
                withContext(Dispatchers.Main) {
                    data?.let {
                        uploadRequestData(it, token)
                    }
                }
            }
        }
    }

    private fun uploadRequestData(it: RequestMaterialModel, token: String?) {
        val query = HashMap<String, Any>()
        query["apiToken"] = token ?: ""
        query["product_id"] = it.product_id ?: ""
        query["product_name"] = it.product_name ?: ""
        query["crop_id"] = it.crop_id ?: ""
        query["crop_name"] = it.crop_name ?: ""
        query["expected_demos"] = it.expected_demos ?: ""
        query["need_by_date"] = it.need_by_date ?: ""
        query["requested_units"] = it.requested_units ?: ""
        query["godown_id"] = it.godown_id ?: ""
        query["godown_name"] = it.godown_name ?: ""
        val currentHitTime = System.currentTimeMillis()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.requestMaterialModelDao().updateRequestMaterialData(
                    it.id,
                    currentHitTime,
                    true,
                )
            }
        }
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).updateMaterialRequestData(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonAtpResponse>() {
                override fun onCompleted() {
                }

                override fun onError(e: Throwable?) {
                    GlobalScope.launch {
                        withContext(Dispatchers.Main) {
                            uploadRequestMaterial()
                        }.run {
                            withContext(Dispatchers.IO) {
                                val currentHitTimeTwoHour =
                                    System.currentTimeMillis() + (121 * 60 * 1000)
                                appDatabase.requestMaterialModelDao().updateRequestMaterialData(
                                    it.id,
                                    currentHitTimeTwoHour,
                                    false,
                                )
                            }
                        }
                    }
                    logEventCustom(
                        e?.localizedMessage ?: "unknown error occurred",
                        null,
                        "v3/requestNewMaterial"
                    )
                }

                override fun onNext(response: CommonAtpResponse?) {
                    response?.let { responseData ->
                        GlobalScope.launch {
                            if (responseData.status_code == 200) {
                                withContext(Dispatchers.IO) {
                                    val dataGot: RequestMaterialModel? =
                                        appDatabase.requestMaterialModelDao()
                                            .getSingleRequestMaterial(true)
                                    dataGot?.let {
                                        appDatabase.requestMaterialModelDao()
                                            .deleteTableData(
                                                it.id
                                            )
                                    }
                                }.run {
                                    withContext(Dispatchers.Main) {
                                        uploadRequestMaterial()
                                    }
                                }
                            } else {
                                withContext(Dispatchers.Main) {
                                    uploadRequestMaterial()
                                    logEventCustom(
                                        response.message,
                                        null,
                                        "v3/requestNewMaterial"
                                    )
                                }.run {
                                    withContext(Dispatchers.IO) {
                                        val currentHitTimeTwoHour =
                                            System.currentTimeMillis() + (121 * 60 * 1000)
                                        appDatabase.requestMaterialModelDao()
                                            .updateRequestMaterialData(
                                                it.id,
                                                currentHitTimeTwoHour,
                                                false,
                                            )
                                    }
                                }
                            }
                        }
                    }
                }
            })
    }

    //Get Company,Designation,Fl data
    fun companyFlDesignationData() {
        val token = preference.getString("token")
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = getLastHitTimeData("v3/getTypeDesignationFL")
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_date"] = time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).getCompanyDesignationFl(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<TypeDesignationFLModel>() {
                            override fun onCompleted() {

                            }

                            override fun onError(e: Throwable?) {

                            }

                            override fun onNext(it: TypeDesignationFLModel?) {
                                getTeaEstateStgListData()
                                getTeaEstateStateDropDownListData()
                                if (it != null) {
                                    if (it.status_code == 200) {
                                        insertDataInDb(it.data)
                                    } else if (it.status_code == 409) {
                                        EventBus.getDefault().post("logout")
                                    }
                                }
                            }

                        })
                }
            }
        }
    }

    private fun insertDataInDb(data: com.dhanuka.indhan.institutional.model.Data?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                "v3/getTypeDesignationFL".updateTimeOnDb()
                data?.company_data?.let {
                    it.forEach { company ->
                        appDatabase.companyTypeDataListDao().insert(company)
                    }
                }

                data?.designation_data?.let {
                    it.forEach { designation ->
                        appDatabase.designationDataListDao().insert(designation)
                    }
                }

                data?.fl_data?.let {
                    it.forEach { fl ->
                        appDatabase.flDataListDao().insert(fl)
                    }
                }

                data?.category_data?.let {
                    it.forEach { it1 ->
                        appDatabase.categoryDataDao().insert(it1)
                    }
                }
            }
        }
    }

    private fun getTeaEstateStgListData() {
        val token = preference.getString("token")
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = getLastHitTimeData("v3/teaEstateListing")
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_at"] = time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).getTeaEstateStgList(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<TeaEstateStgListingResponse>() {
                            override fun onCompleted() {
                            }

                            override fun onError(e: Throwable?) {
                            }

                            override fun onNext(it: TeaEstateStgListingResponse?) {
                                if (it != null) {
                                    if (it.status_code == 200) {
                                        insertTeaDataInDb(it.data)
                                    } else if (it.status_code == 409) {
                                        EventBus.getDefault().post("logout")
                                    }
                                }
                            }

                        })
                }
            }
        }

    }

    private fun insertTeaDataInDb(data: List<TeaEstateStgData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                "v3/teaEstateListing".updateTimeOnDb()
                data?.let {
                    it.forEach { teaData ->
                        appDatabase.teaEstateStgListDataDao().insert(teaData)
                    }
                }
            }
        }
    }


    private fun getTeaEstateStateDropDownListData() {
        val token = preference.getString("token")
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = getLastHitTimeData("v3/teaEstateListingHeadOfc")
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_at"] = time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).getTeaEstateStgHeadOfficeList(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<TeaEstateStgHeadOfficeListResponse>() {
                            override fun onCompleted() {

                            }

                            override fun onError(e: Throwable?) {

                            }

                            override fun onNext(it: TeaEstateStgHeadOfficeListResponse?) {
                                if (it != null) {
                                    if (it.status_code == 200) {
                                        insertTeaEstateStateDropDownDataInDb(it.data)
                                    } else if (it.status_code == 409) {
                                        EventBus.getDefault().post("logout")
                                    }
                                }
                            }

                        })
                }
            }
        }

    }

    private fun insertTeaEstateStateDropDownDataInDb(data: List<TeaEstateStgHeadOfficeData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                "v3/teaEstateListingHeadOfc".updateTimeOnDb()
                data?.let {
                    it.forEach { teaData ->
                        appDatabase.teaEstateStgDropDownListDataDao().insert(teaData)
                    }
                }
            }
        }
    }

    //Reset TeaEstateData
    fun resetAddTeaEstateBranchData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (15 * 60 * 1000)
                val listOfDbData =
                    appDatabase.addTeaEstateStgBranchDao().getAllAddTeaEstateStgBranchData()
                listOfDbData?.forEach {
                    appDatabase.addTeaEstateStgBranchDao().updateAddTeaEstateStgBranchData(
                        it.teaEstateId,
                        currentHitTime,
                        0,
                    )
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    callApiUpdateTeaEstate()
                }
            }
        }
    }

    //check call TeaEstateData
    fun callApiUpdateTeaEstate() {
        var teaEstate: AddTeaEstateStgBranchModel? = null
        val token = preference.getString("token")
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis()
                teaEstate =
                    appDatabase.addTeaEstateStgBranchDao()
                        .getSingleAddTeaEstateStgBranchData(0, currentHitTime)
            }.run {
                withContext(Dispatchers.Main) {
                    teaEstate?.let {
                        apiCallAddTeaEstate(it, token)
                    }
                }
            }
        }

    }

    //Api call TeaEstateData
    private fun apiCallAddTeaEstate(data: AddTeaEstateStgBranchModel, token: String) {
        val map: HashMap<String, RequestBody> = HashMap()
        map["apiToken"] = RequestBody.create(MediaType.parse("text/plain"), token)
        map["office_type"] =
            RequestBody.create(MediaType.parse("text/plain"), data.office_type.toString())
        map["tea_estate_type"] =
            RequestBody.create(MediaType.parse("text/plain"), data.tea_estate_type.toString())
        map["parent_tea_estate_id"] =
            RequestBody.create(MediaType.parse("text/plain"), data.parent_tea_estate_id ?: "0")
        map["name"] =
            RequestBody.create(MediaType.parse("text/plain"), data.name ?: "")
        map["turn_over"] =
            RequestBody.create(MediaType.parse("text/plain"), data.turn_over ?: "")
        map["land_area"] =
            RequestBody.create(MediaType.parse("text/plain"), data.land_area ?: "")
        map["estate_company_id"] =
            RequestBody.create(MediaType.parse("text/plain"), data.estate_category_id.toString())
        map["state_id"] =
            RequestBody.create(MediaType.parse("text/plain"), data.state_id.toString())
        map["district_id"] =
            RequestBody.create(MediaType.parse("text/plain"), data.district_id.toString())
        map["address"] = RequestBody.create(MediaType.parse("text/plain"), data.address ?: "")
        map["contact_data"] =
            RequestBody.create(MediaType.parse("text/plain"), data.contact_data ?: "")
        map["fl_name"] = RequestBody.create(MediaType.parse("text/plain"), data.fl_name ?: "")
        map["fl_ecn"] = RequestBody.create(MediaType.parse("text/plain"), data.fl_ecn ?: "")
        map["fl_type"] = RequestBody.create(MediaType.parse("text/plain"), data.fl_type ?: "")
        map["latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.latitude.toString())
        map["longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.longitude.toString())

        val file = File(data.image!!)
        val fileBody1 = RequestBody.create(MediaType.parse("image/*"), file)
        val imagePart1: MultipartBody.Part =
            MultipartBody.Part.createFormData("image", file.name, fileBody1)

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.addTeaEstateStgBranchDao()
                    .updateAddTeaEstateStgBranchData(data.teaEstateId, data.hitTime ?: 0, 1)
            }
        }

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).addTeaEstateData(map, imagePart1)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonAtpResponse>() {

                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    GlobalScope.launch {
                        withContext(Dispatchers.Main) {
                            callApiUpdateTeaEstate()
                        }.run {
                            withContext(Dispatchers.IO) {
                                val currentHitTimeTwoHour =
                                    System.currentTimeMillis() + (121 * 60 * 1000)
                                appDatabase.addTeaEstateStgBranchDao()
                                    .updateAddTeaEstateStgBranchData(
                                        data.teaEstateId,
                                        currentHitTimeTwoHour,
                                        0
                                    )
                            }
                        }
                    }
                }

                override fun onNext(teaData: CommonAtpResponse?) {
                    teaData.let {
                        if (it != null) {
                            if (it.status_code == 200) {
                                GlobalScope.launch {
                                    withContext(Dispatchers.IO) {
                                        appDatabase.addTeaEstateStgBranchDao()
                                            .deleteAddTeaEstateStgBranchData(
                                                data.teaEstateId
                                            )
                                    }.run {
                                        withContext(Dispatchers.Main) {
                                            callApiUpdateTeaEstate()
                                            getTeaEstateStgListData()
                                        }
                                    }
                                }
                            } else {
                                GlobalScope.launch {
                                    withContext(Dispatchers.Main) {
                                        callApiUpdateTeaEstate()
                                    }.run {
                                        withContext(Dispatchers.IO) {
                                            val currentHitTimeTwoHour =
                                                System.currentTimeMillis() + (121 * 60 * 1000)
                                            appDatabase.addTeaEstateStgBranchDao()
                                                .updateAddTeaEstateStgBranchData(
                                                    data.teaEstateId,
                                                    currentHitTimeTwoHour,
                                                    0
                                                )
                                        }
                                    }
                                }
                            }
                        } else {
                            GlobalScope.launch {
                                withContext(Dispatchers.Main) {
                                    callApiUpdateTeaEstate()
                                }.run {
                                    withContext(Dispatchers.IO) {
                                        val currentHitTimeTwoHour =
                                            System.currentTimeMillis() + (121 * 60 * 1000)
                                        appDatabase.addTeaEstateStgBranchDao()
                                            .updateAddTeaEstateStgBranchData(
                                                data.teaEstateId,
                                                currentHitTimeTwoHour,
                                                0
                                            )
                                    }
                                }
                            }
                        }
                    }
                }
            })
    }

    //reset online arrival departure
    fun resetArrivalDepartureAtpTeaEstateData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (15 * 60 * 1000)
                val listOfDbData =
                    appDatabase.markArrivalDepartureAtpDao().getAllArrivalDepartureListData()
                listOfDbData?.forEach {
                    it?.let { it2 ->
                        appDatabase.markArrivalDepartureAtpDao().updateArrivalDeparture(
                            it2.atp_id,
                            currentHitTime,
                            0,
                            it2.ticket_number!!,
                        )
                    }
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    uploadServiceArrivalDepartureAtpTeaEstate()
                }
            }
        }
    }

    //for Arrival departure online
    fun uploadArrivalDepartureAtpTeaEstateData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (24 * 60 * 60 * 1000)
                val data: TeaEstateMarkArrivalDepartureAtpModel? =
                    appDatabase.markArrivalDepartureAtpDao()
                        .getSosArrivalDepartureData(currentHitTime)
                if (data != null) {
                    val listOfDbData =
                        appDatabase.markArrivalDepartureAtpDao()
                            .getAllArrivalDepartureListData()
                    listOfDbData?.forEach {
                        val currentHitTimeTwoHour = System.currentTimeMillis()
                        appDatabase.markArrivalDepartureAtpDao().updateArrivalDeparture(
                            it?.atp_id!!,
                            currentHitTimeTwoHour,
                            0,
                            it.ticket_number!!,
                        )
                    }
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    uploadServiceArrivalDepartureAtpTeaEstate()
                }
            }
        }
    }

    private fun uploadServiceArrivalDepartureAtpTeaEstate() {
        val token = preference.getString("token")
        GlobalScope.launch {
            var data: TeaEstateMarkArrivalDepartureAtpModel? = null
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis()
                data = appDatabase.markArrivalDepartureAtpDao()
                    .getSingleArrivalDepartureData(0, currentHitTime, 2)
            }.run {
                withContext(Dispatchers.Main) {
                    data?.let {
                        multipleFileUploadWebserviceAtpTeaEstate(it, token)
                    }
                }
            }
        }
    }

    private fun multipleFileUploadWebserviceAtpTeaEstate(
        data: TeaEstateMarkArrivalDepartureAtpModel,
        token: String
    ) {
        val map: MutableMap<String, RequestBody> = HashMap()
        map["apiToken"] = RequestBody.create(MediaType.parse("text/plain"), token)
        map["estate_atp_id"] =
            RequestBody.create(MediaType.parse("text/plain"), (data.atp_id).toString())
        map["ticket_number"] =
            RequestBody.create(MediaType.parse("text/plain"), data.ticket_number ?: "")

        map["arrival_time"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_date ?: "")
        map["arrival_latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_lat ?: "")
        map["arrival_longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_lng ?: "")
        map["arrival_comment"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_comment ?: "")

        map["departure_time"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_date ?: "")
        map["departure_latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_lat ?: "")
        map["departure_longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_lng ?: "")
        map["departure_comment"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_comment ?: "")

        map["duration"] = RequestBody.create(MediaType.parse("text/plain"), data.duration)

        val parts: ArrayList<MultipartBody.Part> = ArrayList()
        val arrivalFile = File(data.arrival_image ?: "")
        val departureFile = File(data.departure_image ?: "")
        try {
            val requestFile1: RequestBody =
                RequestBody.create(MediaType.parse("image/*"), arrivalFile)
            val part =
                MultipartBody.Part.createFormData(
                    "arrival_image",
                    arrivalFile.name,
                    requestFile1
                )
            parts.add(part)

            val requestFile2: RequestBody =
                RequestBody.create(MediaType.parse("image/*"), departureFile)
            val part2 =
                MultipartBody.Part.createFormData(
                    "departure_image",
                    departureFile.name,
                    requestFile2
                )
            parts.add(part2)
        } catch (e: JSONException) {
            e.printStackTrace()
        }

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.markArrivalDepartureAtpDao()
                    .updateArrivalDepartureData(data.atp_id, 1)
            }
        }

        val okHttpClient = RetrofitClient.httpClient
        val builder: Retrofit.Builder = Retrofit.Builder()
            .baseUrl(RetrofitClient.LIVE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
        val retrofit = builder.build()
        val client: ApiServiceRequest = retrofit.create(
            ApiServiceRequest::class.java
        )
        val call: Call<ArrivalDepartureResponse> = client.arrivalDeparture(map, parts)
        call.enqueue(object : Callback<ArrivalDepartureResponse?> {
            override fun onResponse(
                call: Call<ArrivalDepartureResponse?>,
                response: Response<ArrivalDepartureResponse?>
            ) {
                if (response.body() != null) {
                    val model: ArrivalDepartureResponse? = response.body()
                    if (model != null) {
                        GlobalScope.launch {
                            if (model.status_code == 200) {
                                withContext(Dispatchers.IO) {
                                    data.let {
                                        appDatabase.markArrivalDepartureAtpDao()
                                            .deleteTableData(
                                                it.atp_id
                                            )
                                    }
                                }.run {
                                    withContext(Dispatchers.Main) {
                                        uploadServiceArrivalDepartureAtpTeaEstate()
                                    }
                                }
                            } else {
                                withContext(Dispatchers.IO) {
                                    val currentHitTimeTwoHour =
                                        System.currentTimeMillis() + (121 * 60 * 1000)
                                    appDatabase.markArrivalDepartureAtpDao()
                                        .updateArrivalDeparture(
                                            data.atp_id,
                                            currentHitTimeTwoHour,
                                            0,
                                            data.ticket_number!!
                                        )
                                }.run {
                                    delay(1000)
                                    withContext(Dispatchers.Main) {
                                        uploadServiceArrivalDepartureAtpTeaEstate()
                                    }
                                }
                            }
                        }
                    }
                }
            }

            override fun onFailure(call: Call<ArrivalDepartureResponse?>, t: Throwable) {
                GlobalScope.launch {
                    withContext(Dispatchers.Main) {
                        uploadServiceArrivalDepartureAtpTeaEstate()
                    }.run {
                        withContext(Dispatchers.IO) {
                            val currentHitTimeTwoHour =
                                System.currentTimeMillis() + (121 * 60 * 1000)
                            appDatabase.markArrivalDepartureAtpDao()
                                .updateArrivalDeparture(
                                    data.atp_id,
                                    currentHitTimeTwoHour,
                                    0,
                                    data.ticket_number!!
                                )
                        }
                    }

                }
            }
        })
    }


    //reset online arrival departure
    fun resetArrivalDepartureDistributorData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (15 * 60 * 1000)
                val listOfDbData =
                    appDatabase.arrivalDepartureDistributorDao().getAllArrivalDepartureListData()
                listOfDbData?.forEach {
                    it?.let { it2 ->
                        appDatabase.arrivalDepartureDistributorDao().updateArrivalDeparture(
                            it2.distributor_id,
                            currentHitTime,
                            0
                        )
                    }
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    uploadArrivalDepartureDistributorData()
                }
            }
        }
    }

    //for distributor Arrival departure online
    fun uploadArrivalDepartureDistributorData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (24 * 60 * 60 * 1000)
                val data: MarkArrivalDepartureDistributor? =
                    appDatabase.arrivalDepartureDistributorDao()
                        .getSosArrivalDepartureData(currentHitTime)
                if (data != null) {
                    val listOfDbData =
                        appDatabase.arrivalDepartureDistributorDao()
                            .getAllArrivalDepartureListData()
                    listOfDbData?.forEach {
                        val currentHitTimeTwoHour = System.currentTimeMillis()
                        appDatabase.arrivalDepartureDistributorDao().updateArrivalDeparture(
                            it?.distributor_id!!,
                            currentHitTimeTwoHour,
                            0
                        )
                    }
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    uploadServiceArrivalDepartureDistributor()
                }
            }
        }
    }

    private fun uploadServiceArrivalDepartureDistributor() {
        val token = preference.getString("token")
        GlobalScope.launch {
            var data: MarkArrivalDepartureDistributor? = null
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis()
                data = appDatabase.arrivalDepartureDistributorDao()
                    .getSingleArrivalDepartureData(0, currentHitTime, 2)
            }.run {
                withContext(Dispatchers.Main) {
                    data?.let {
                        multipleFileUploadWebserviceDistributor(it, token)
                    }
                }
            }
        }
    }

    private fun multipleFileUploadWebserviceDistributor(
        data: MarkArrivalDepartureDistributor,
        token: String
    ) {
        val map: MutableMap<String, RequestBody> = HashMap()
        map["apiToken"] = RequestBody.create(MediaType.parse("text/plain"), token)

        map["distributor_advisor_id"] =
            RequestBody.create(MediaType.parse("text/plain"), (data.distributor_id).toString())

        map["arrival_time"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_date ?: "")
        map["arrival_latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_lat ?: "")
        map["arrival_longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_lng ?: "")
        map["arrival_comment"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_comment ?: "")

        map["departure_time"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_date ?: "")
        map["departure_latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_lat ?: "")
        map["departure_longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_lng ?: "")
        map["departure_comment"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_comment ?: "")

        map["duration"] = RequestBody.create(MediaType.parse("text/plain"), data.duration)

        val parts: ArrayList<MultipartBody.Part> = ArrayList()
        val arrivalFile = File(data.arrival_image ?: "")
        val departureFile = File(data.departure_image ?: "")
        try {
            val requestFile1: RequestBody =
                RequestBody.create(MediaType.parse("image/*"), arrivalFile)
            val part =
                MultipartBody.Part.createFormData(
                    "arrival_image",
                    arrivalFile.name,
                    requestFile1
                )
            parts.add(part)

            val requestFile2: RequestBody =
                RequestBody.create(MediaType.parse("image/*"), departureFile)
            val part2 =
                MultipartBody.Part.createFormData(
                    "departure_image",
                    departureFile.name,
                    requestFile2
                )
            parts.add(part2)
        } catch (e: JSONException) {
            e.printStackTrace()
        }

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.arrivalDepartureDistributorDao()
                    .updateArrivalDepartureData(data.distributor_id, 1)
            }
        }

        val okHttpClient = RetrofitClient.httpClient
        val builder: Retrofit.Builder = Retrofit.Builder()
            .baseUrl(RetrofitClient.LIVE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
        val retrofit = builder.build()
        val client: ApiServiceRequest = retrofit.create(
            ApiServiceRequest::class.java
        )
        val call: Call<ArrivalDepartureResponse> = client.arrivalDepartureDistributor(map, parts)
        call.enqueue(object : Callback<ArrivalDepartureResponse?> {
            override fun onResponse(
                call: Call<ArrivalDepartureResponse?>,
                response: Response<ArrivalDepartureResponse?>
            ) {
                if (response.body() != null) {
                    val model: ArrivalDepartureResponse? = response.body()
                    if (model != null) {
                        GlobalScope.launch {
                            if (model.status_code == 200) {
                                withContext(Dispatchers.IO) {
                                    data.let {
                                        appDatabase.arrivalDepartureDistributorDao()
                                            .deleteTableData(
                                                it.distributor_id
                                            )
                                    }
                                }.run {
                                    withContext(Dispatchers.Main) {
                                        uploadServiceArrivalDepartureDistributor()
                                    }
                                }
                            } else {
                                withContext(Dispatchers.IO) {
                                    val currentHitTimeTwoHour =
                                        System.currentTimeMillis() + (121 * 60 * 1000)
                                    appDatabase.arrivalDepartureDistributorDao()
                                        .updateArrivalDeparture(
                                            data.distributor_id,
                                            currentHitTimeTwoHour,
                                            0
                                        )
                                }.run {
                                    delay(1000)
                                    withContext(Dispatchers.Main) {
                                        uploadServiceArrivalDepartureDistributor()
                                    }
                                }
                            }
                        }
                    }
                }
            }

            override fun onFailure(call: Call<ArrivalDepartureResponse?>, t: Throwable) {
                GlobalScope.launch {
                    withContext(Dispatchers.Main) {
                        uploadServiceArrivalDepartureAtpTeaEstate()
                    }.run {
                        withContext(Dispatchers.IO) {
                            val currentHitTimeTwoHour =
                                System.currentTimeMillis() + (121 * 60 * 1000)
                            appDatabase.arrivalDepartureDistributorDao()
                                .updateArrivalDeparture(
                                    data.distributor_id,
                                    currentHitTimeTwoHour,
                                    0
                                )
                        }
                    }

                }
            }
        })
    }


    //Reset SugarMillData
    fun resetAddSugarMillBranchData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (15 * 60 * 1000)
                val listOfDbData =
                    appDatabase.addSugarMillBranchDao().getAllAddSugarMillBranchData()
                listOfDbData?.forEach {
                    appDatabase.addSugarMillBranchDao().updateAddSugarMillBranchData(
                        it.sugarmillId,
                        currentHitTime,
                        0,
                    )
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    callApiUpdateSugarMill()
                }
            }
        }
    }

    //check call SugarMillData
    fun callApiUpdateSugarMill() {
        var sugarMill: AddSugarMillBranchModel? = null
        val token = preference.getString("token")
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis()
                sugarMill =
                    appDatabase.addSugarMillBranchDao()
                        .getSingleAddSugarMillBranchData(0, currentHitTime)
            }.run {
                withContext(Dispatchers.Main) {
                    sugarMill?.let {
                        apiCallAddSugarMill(it, token)
                    }
                }
            }
        }

    }

    //Api call SugarMillData
    private fun apiCallAddSugarMill(data: AddSugarMillBranchModel, token: String) {
        val map: HashMap<String, RequestBody> = HashMap()
        map["apiToken"] = RequestBody.create(MediaType.parse("text/plain"), token)
        map["office_type"] =
            RequestBody.create(MediaType.parse("text/plain"), data.office_type.toString())
        map["sugarmill_type"] =
            RequestBody.create(MediaType.parse("text/plain"), data.sugarmill_type.toString())
        map["parent_sugarmill_id"] =
            RequestBody.create(MediaType.parse("text/plain"), data.parent_sugarmill_id ?: "0")
        map["name"] =
            RequestBody.create(MediaType.parse("text/plain"), data.name ?: "")
        map["turn_over"] =
            RequestBody.create(MediaType.parse("text/plain"), data.turn_over ?: "")
        map["land_area"] =
            RequestBody.create(MediaType.parse("text/plain"), data.land_area ?: "")
        map["sugarmill_company_id"] =
            RequestBody.create(MediaType.parse("text/plain"), data.sugarmill_category_id.toString())
        map["state_id"] =
            RequestBody.create(MediaType.parse("text/plain"), data.state_id.toString())
        map["district_id"] =
            RequestBody.create(MediaType.parse("text/plain"), data.district_id.toString())
        map["address"] = RequestBody.create(MediaType.parse("text/plain"), data.address ?: "")
        map["contact_data"] =
            RequestBody.create(MediaType.parse("text/plain"), data.contact_data ?: "")
        map["fl_name"] = RequestBody.create(MediaType.parse("text/plain"), data.fl_name ?: "")
        map["fl_ecn"] = RequestBody.create(MediaType.parse("text/plain"), data.fl_ecn ?: "")
        map["fl_type"] = RequestBody.create(MediaType.parse("text/plain"), data.fl_type ?: "")
        map["latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.latitude.toString())
        map["longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.longitude.toString())

        val file = File(data.image!!)
        val fileBody1 = RequestBody.create(MediaType.parse("image/*"), file)
        val imagePart1: MultipartBody.Part =
            MultipartBody.Part.createFormData("image", file.name, fileBody1)

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.addSugarMillBranchDao()
                    .updateAddSugarMillBranchData(data.sugarmillId, data.hitTime ?: 0, 1)
            }
        }

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).addSugarMillData(map, imagePart1)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonAtpResponse>() {

                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    GlobalScope.launch {
                        withContext(Dispatchers.Main) {
                            callApiUpdateSugarMill()
                        }.run {
                            withContext(Dispatchers.IO) {
                                val currentHitTimeTwoHour =
                                    System.currentTimeMillis() + (121 * 60 * 1000)
                                appDatabase.addSugarMillBranchDao()
                                    .updateAddSugarMillBranchData(
                                        data.sugarmillId,
                                        currentHitTimeTwoHour,
                                        0
                                    )
                            }
                        }
                    }
                }

                override fun onNext(teaData: CommonAtpResponse?) {
                    teaData.let {
                        if (it != null) {
                            if (it.status_code == 200) {
                                GlobalScope.launch {
                                    withContext(Dispatchers.IO) {
                                        appDatabase.addSugarMillBranchDao()
                                            .deleteAddSugarMillBranchData(
                                                data.sugarmillId
                                            )
                                    }.run {
                                        withContext(Dispatchers.Main) {
                                            callApiUpdateSugarMill()
                                        }
                                    }
                                }
                            } else {
                                GlobalScope.launch {
                                    withContext(Dispatchers.Main) {
                                        callApiUpdateSugarMill()
                                    }.run {
                                        withContext(Dispatchers.IO) {
                                            val currentHitTimeTwoHour =
                                                System.currentTimeMillis() + (121 * 60 * 1000)
                                            appDatabase.addSugarMillBranchDao()
                                                .updateAddSugarMillBranchData(
                                                    data.sugarmillId,
                                                    currentHitTimeTwoHour,
                                                    0
                                                )
                                        }
                                    }
                                }
                            }
                        } else {
                            GlobalScope.launch {
                                withContext(Dispatchers.Main) {
                                    callApiUpdateSugarMill()
                                }.run {
                                    withContext(Dispatchers.IO) {
                                        val currentHitTimeTwoHour =
                                            System.currentTimeMillis() + (121 * 60 * 1000)
                                        appDatabase.addSugarMillBranchDao()
                                            .updateAddSugarMillBranchData(
                                                data.sugarmillId,
                                                currentHitTimeTwoHour,
                                                0
                                            )
                                    }
                                }
                            }
                        }
                    }
                }
            })
    }

    //reset online arrival departure SugarMill
    fun resetArrivalDepartureAtpSugarMillData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (15 * 60 * 1000)
                val listOfDbData =
                    appDatabase.markArrivalDepartureAtpSugarMillDao()
                        .getAllArrivalDepartureListData()
                listOfDbData?.forEach {
                    it?.let { it2 ->
                        appDatabase.markArrivalDepartureAtpSugarMillDao().updateArrivalDeparture(
                            it2.sugarmill_atp_id,
                            currentHitTime,
                            0,
                            it2.ticket_number!!,
                        )
                    }
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    uploadServiceArrivalDepartureAtpSugarMill()
                }
            }
        }
    }

    //upload arrival departure sugar mill
    fun uploadArrivalDepartureAtpSugarMillData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (24 * 60 * 60 * 1000)
                val data: SugarMillMarkArrivalDepartureAtpModel? =
                    appDatabase.markArrivalDepartureAtpSugarMillDao()
                        .getSosArrivalDepartureData(currentHitTime)
                if (data != null) {
                    val listOfDbData =
                        appDatabase.markArrivalDepartureAtpSugarMillDao()
                            .getAllArrivalDepartureListData()
                    listOfDbData?.forEach {
                        val currentHitTimeTwoHour = System.currentTimeMillis()
                        appDatabase.markArrivalDepartureAtpSugarMillDao().updateArrivalDeparture(
                            it?.sugarmill_atp_id!!,
                            currentHitTimeTwoHour,
                            0,
                            it.ticket_number!!,
                        )
                    }
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    uploadServiceArrivalDepartureAtpSugarMill()
                }
            }
        }
    }

    private fun uploadServiceArrivalDepartureAtpSugarMill() {
        val token = preference.getString("token")
        GlobalScope.launch {
            var data: SugarMillMarkArrivalDepartureAtpModel? = null
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis()
                data = appDatabase.markArrivalDepartureAtpSugarMillDao()
                    .getSingleArrivalDepartureData(0, currentHitTime, 2)
            }.run {
                withContext(Dispatchers.Main) {
                    data?.let {
                        multipleFileUploadWebserviceAtpSugarMill(it, token)
                    }
                }
            }
        }
    }

    private fun multipleFileUploadWebserviceAtpSugarMill(
        data: SugarMillMarkArrivalDepartureAtpModel,
        token: String
    ) {
        val map: MutableMap<String, RequestBody> = HashMap()
        map["apiToken"] = RequestBody.create(MediaType.parse("text/plain"), token)
        map["sugarmill_atp_id"] =
            RequestBody.create(MediaType.parse("text/plain"), (data.sugarmill_atp_id).toString())
        map["ticket_number"] =
            RequestBody.create(MediaType.parse("text/plain"), data.ticket_number ?: "")

        map["arrival_time"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_date ?: "")
        map["arrival_latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_lat ?: "")
        map["arrival_longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_lng ?: "")
        map["arrival_comment"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_comment ?: "")

        map["departure_time"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_date ?: "")
        map["departure_latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_lat ?: "")
        map["departure_longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_lng ?: "")
        map["departure_comment"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_comment ?: "")

        map["duration"] = RequestBody.create(MediaType.parse("text/plain"), data.duration)

        val parts: ArrayList<MultipartBody.Part> = ArrayList()
        val arrivalFile = File(data.arrival_image ?: "")
        val departureFile = File(data.departure_image ?: "")
        try {
            val requestFile1: RequestBody =
                RequestBody.create(MediaType.parse("image/*"), arrivalFile)
            val part =
                MultipartBody.Part.createFormData(
                    "arrival_image",
                    arrivalFile.name,
                    requestFile1
                )
            parts.add(part)

            val requestFile2: RequestBody =
                RequestBody.create(MediaType.parse("image/*"), departureFile)
            val part2 =
                MultipartBody.Part.createFormData(
                    "departure_image",
                    departureFile.name,
                    requestFile2
                )
            parts.add(part2)
        } catch (e: JSONException) {
            e.printStackTrace()
        }

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.markArrivalDepartureAtpSugarMillDao()
                    .updateArrivalDepartureData(data.sugarmill_atp_id, 1)
            }
        }

        val okHttpClient = RetrofitClient.httpClient
        val builder: Retrofit.Builder = Retrofit.Builder()
            .baseUrl(RetrofitClient.LIVE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
        val retrofit = builder.build()
        val client: ApiServiceRequest = retrofit.create(
            ApiServiceRequest::class.java
        )
        val call: Call<ArrivalDepartureResponse> = client.arrivalDepartureSugarMill(map, parts)
        call.enqueue(object : Callback<ArrivalDepartureResponse?> {
            override fun onResponse(
                call: Call<ArrivalDepartureResponse?>,
                response: Response<ArrivalDepartureResponse?>
            ) {
                if (response.body() != null) {
                    val model: ArrivalDepartureResponse? = response.body()
                    if (model != null) {
                        GlobalScope.launch {
                            if (model.status_code == 200) {
                                withContext(Dispatchers.IO) {
                                    data.let {
                                        appDatabase.markArrivalDepartureAtpSugarMillDao()
                                            .deleteTableData(
                                                it.sugarmill_atp_id
                                            )
                                    }
                                }.run {
                                    withContext(Dispatchers.Main) {
                                        uploadServiceArrivalDepartureAtpSugarMill()
                                    }
                                }
                            } else {
                                withContext(Dispatchers.IO) {
                                    val currentHitTimeTwoHour =
                                        System.currentTimeMillis() + (121 * 60 * 1000)
                                    appDatabase.markArrivalDepartureAtpSugarMillDao()
                                        .updateArrivalDeparture(
                                            data.sugarmill_atp_id,
                                            currentHitTimeTwoHour,
                                            0,
                                            data.ticket_number!!
                                        )
                                }.run {
                                    delay(1000)
                                    withContext(Dispatchers.Main) {
                                        uploadServiceArrivalDepartureAtpSugarMill()
                                    }
                                }
                            }
                        }
                    }
                }
            }

            override fun onFailure(call: Call<ArrivalDepartureResponse?>, t: Throwable) {
                GlobalScope.launch {
                    withContext(Dispatchers.Main) {
                        uploadServiceArrivalDepartureAtpSugarMill()
                    }.run {
                        withContext(Dispatchers.IO) {
                            val currentHitTimeTwoHour =
                                System.currentTimeMillis() + (121 * 60 * 1000)
                            appDatabase.markArrivalDepartureAtpSugarMillDao()
                                .updateArrivalDeparture(
                                    data.sugarmill_atp_id,
                                    currentHitTimeTwoHour,
                                    0,
                                    data.ticket_number!!
                                )
                        }
                    }

                }
            }
        })
    }

    fun companyFlDesignationDataSugarMill() {
        val token = preference.getString("token")
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = getLastHitTimeData("v3/getSugarmillTypeDesignationFL")
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_date"] = time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java)
                        .getSuagrMillCompanyDesignationFl(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<SugarMillTypeDesignationFlModel>() {
                            override fun onCompleted() {

                            }

                            override fun onError(e: Throwable?) {

                            }

                            override fun onNext(it: SugarMillTypeDesignationFlModel?) {
                                getSugarMillListData()
                                getSugarMillListDropDownData()
                                if (it != null) {
                                    if (it.status_code == 200) {
                                        insertSugarMillDataInDb(it.data)
                                    } else if (it.status_code == 409) {
                                        EventBus.getDefault().post("logout")
                                    }
                                }
                            }

                        })
                }
            }
        }
    }

    private fun insertSugarMillDataInDb(data: com.dhanuka.indhan.institutional.model.sugarMill.Data?) {

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                "v3/getSugarmillTypeDesignationFL".updateTimeOnDb()
                data?.company_data?.let {
                    it.forEach { company ->
                        appDatabase.companyTypeDataListSugarMillDao().insert(company)
                    }
                }

                data?.designation_data?.let {
                    it.forEach { designation ->
                        appDatabase.designationDataListSugarMillDao().insert(designation)
                    }
                }

                data?.fl_data?.let {
                    it.forEach { fl ->
                        appDatabase.flDataListSugarMillDao().insert(fl)
                    }
                }

                data?.category_data?.let {
                    it.forEach { it1 ->
                        appDatabase.categoryDataSugarMillDao().insert(it1)
                    }
                }
            }
        }
    }

    private fun getSugarMillListData() {
        val token = preference.getString("token")
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = getLastHitTimeData("v3/sugarmillListing")
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_at"] = time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).getSugarMillList(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<SugarMillListingResponse>() {
                            override fun onCompleted() {

                            }

                            override fun onError(e: Throwable?) {

                            }

                            override fun onNext(it: SugarMillListingResponse?) {
                                if (it != null) {
                                    if (it.status_code == 200) {
                                        insertSugarMillDataInDb(it.data)
                                    } else if (it.status_code == 409) {
                                        EventBus.getDefault().post("logout")
                                    }
                                }
                            }

                        })
                }
            }
        }

    }

    private fun insertSugarMillDataInDb(data: List<SugarMillData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                "v3/sugarmillListing".updateTimeOnDb()
                data?.let {
                    it.forEach { teaData ->
                        appDatabase.sugarMillListDataDao().insert(teaData)
                    }
                }
            }
        }
    }

    private fun getSugarMillListDropDownData() {
        val token = preference.getString("token")
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = getLastHitTimeData("v3/sugarmillListingHeadOfc")
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_at"] = time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).getSugarMillHeadOfficeList(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<SugarMillHeadOfficeListResponse>() {
                            override fun onCompleted() {

                            }

                            override fun onError(e: Throwable?) {

                            }

                            override fun onNext(it: SugarMillHeadOfficeListResponse?) {
                                if (it != null) {
                                    if (it.status_code == 200) {
                                        insertSugarMillHeadDataInDb(it.data)
                                    } else if (it.status_code == 409) {
                                        EventBus.getDefault().post("logout")
                                    }
                                }
                            }

                        })
                }
            }
        }

    }

    private fun insertSugarMillHeadDataInDb(data: List<SugarMillHeadOfficeData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                "v3/sugarmillListingHeadOfc".updateTimeOnDb()
                data?.let {
                    it.forEach { teaData ->
                        appDatabase.sugarMillHeadOfficeListDataDao().insert(teaData)
                    }
                }
            }
        }
    }

    //Reset InfluencerData
    fun resetAddInfluencersBranchData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (15 * 60 * 1000)
                val listOfDbData =
                    appDatabase.addInfluencersBranchDao().getAllAddInfluencersBranchData()
                listOfDbData?.forEach {
                    appDatabase.addInfluencersBranchDao().updateAddInfluencersBranchData(
                        it.influencerId,
                        currentHitTime,
                        0,
                    )
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    callApiUpdateInfluencers()
                }
            }
        }
    }

    //check call InfluencerData
    fun callApiUpdateInfluencers() {
        var Influencer: AddInfluencersBranchModel? = null
        val token = preference.getString("token")
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis()
                Influencer =
                    appDatabase.addInfluencersBranchDao()
                        .getSingleAddInfluencersBranchData(0, currentHitTime)
            }.run {
                withContext(Dispatchers.Main) {
                    Influencer?.let {
                        apiCallAddInfluencers(it, token)
                    }
                }
            }
        }

    }

    //Api call InfluencerData
    private fun apiCallAddInfluencers(data: AddInfluencersBranchModel, token: String) {
        val map: HashMap<String, RequestBody> = HashMap()
        map["apiToken"] = RequestBody.create(MediaType.parse("text/plain"), token)
        map["office_type"] =
            RequestBody.create(MediaType.parse("text/plain"), data.office_type.toString())
        map["influencer_type"] =
            RequestBody.create(MediaType.parse("text/plain"), data.influencer_type.toString())
        map["parent_influencer_id"] =
            RequestBody.create(MediaType.parse("text/plain"), data.parent_influencer_id ?: "0")
        map["name"] =
            RequestBody.create(MediaType.parse("text/plain"), data.name ?: "")
        map["turn_over"] =
            RequestBody.create(MediaType.parse("text/plain"), data.turn_over ?: "")
        map["land_area"] =
            RequestBody.create(MediaType.parse("text/plain"), data.land_area ?: "")
        map["influencer_company_id"] =
            RequestBody.create(
                MediaType.parse("text/plain"),
                data.influencer_category_id.toString()
            )
        map["state_id"] =
            RequestBody.create(MediaType.parse("text/plain"), data.state_id.toString())
        map["district_id"] =
            RequestBody.create(MediaType.parse("text/plain"), data.district_id.toString())
        map["address"] = RequestBody.create(MediaType.parse("text/plain"), data.address ?: "")
        map["contact_data"] =
            RequestBody.create(MediaType.parse("text/plain"), data.contact_data ?: "")
        map["fl_name"] = RequestBody.create(MediaType.parse("text/plain"), data.fl_name ?: "")
        map["fl_ecn"] = RequestBody.create(MediaType.parse("text/plain"), data.fl_ecn ?: "")
        map["fl_type"] = RequestBody.create(MediaType.parse("text/plain"), data.fl_type ?: "")
        map["latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.latitude.toString())
        map["longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.longitude.toString())

        val file = File(data.image!!)
        val fileBody1 = RequestBody.create(MediaType.parse("image/*"), file)
        val imagePart1: MultipartBody.Part =
            MultipartBody.Part.createFormData("image", file.name, fileBody1)

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.addInfluencersBranchDao()
                    .updateAddInfluencersBranchData(data.influencerId, data.hitTime ?: 0, 1)
            }
        }

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).addInfluencersData(map, imagePart1)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonAtpResponse>() {

                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    GlobalScope.launch {
                        withContext(Dispatchers.Main) {
                            callApiUpdateInfluencers()
                        }.run {
                            withContext(Dispatchers.IO) {
                                val currentHitTimeTwoHour =
                                    System.currentTimeMillis() + (121 * 60 * 1000)
                                appDatabase.addInfluencersBranchDao()
                                    .updateAddInfluencersBranchData(
                                        data.influencerId,
                                        currentHitTimeTwoHour,
                                        0
                                    )
                            }
                        }
                    }
                }

                override fun onNext(teaData: CommonAtpResponse?) {
                    teaData.let {
                        if (it != null) {
                            if (it.status_code == 200) {
                                GlobalScope.launch {
                                    withContext(Dispatchers.IO) {
                                        appDatabase.addInfluencersBranchDao()
                                            .deleteAddInfluencersBranchData(
                                                data.influencerId
                                            )
                                    }.run {
                                        withContext(Dispatchers.Main) {
                                            callApiUpdateInfluencers()
                                        }
                                    }
                                }
                            } else {
                                GlobalScope.launch {
                                    withContext(Dispatchers.Main) {
                                        callApiUpdateInfluencers()
                                    }.run {
                                        withContext(Dispatchers.IO) {
                                            val currentHitTimeTwoHour =
                                                System.currentTimeMillis() + (121 * 60 * 1000)
                                            appDatabase.addInfluencersBranchDao()
                                                .updateAddInfluencersBranchData(
                                                    data.influencerId,
                                                    currentHitTimeTwoHour,
                                                    0
                                                )
                                        }
                                    }
                                }
                            }
                        } else {
                            GlobalScope.launch {
                                withContext(Dispatchers.Main) {
                                    callApiUpdateInfluencers()
                                }.run {
                                    withContext(Dispatchers.IO) {
                                        val currentHitTimeTwoHour =
                                            System.currentTimeMillis() + (121 * 60 * 1000)
                                        appDatabase.addInfluencersBranchDao()
                                            .updateAddInfluencersBranchData(
                                                data.influencerId,
                                                currentHitTimeTwoHour,
                                                0
                                            )
                                    }
                                }
                            }
                        }
                    }
                }
            })
    }

    //reset online arrival departure Influencer
    fun resetArrivalDepartureAtpInfluencersData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (15 * 60 * 1000)
                val listOfDbData =
                    appDatabase.markArrivalDepartureAtpInfluencersDao()
                        .getAllArrivalDepartureListData()
                listOfDbData?.forEach {
                    it?.let { it2 ->
                        appDatabase.markArrivalDepartureAtpInfluencersDao().updateArrivalDeparture(
                            it2.influencer_atp_id,
                            currentHitTime,
                            0,
                            it2.ticket_number!!,
                        )
                    }
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    uploadServiceArrivalDepartureAtpInfluencers()
                }
            }
        }
    }

    //upload arrival departure sugar mill
    fun uploadArrivalDepartureAtpInfluencerData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis() - (24 * 60 * 60 * 1000)
                val data: InfluencersMarkArrivalDepartureAtpModel? =
                    appDatabase.markArrivalDepartureAtpInfluencersDao()
                        .getSosArrivalDepartureData(currentHitTime)
                if (data != null) {
                    val listOfDbData =
                        appDatabase.markArrivalDepartureAtpInfluencersDao()
                            .getAllArrivalDepartureListData()
                    listOfDbData?.forEach {
                        val currentHitTimeTwoHour = System.currentTimeMillis()
                        appDatabase.markArrivalDepartureAtpInfluencersDao().updateArrivalDeparture(
                            it?.influencer_atp_id!!,
                            currentHitTimeTwoHour,
                            0,
                            it.ticket_number!!,
                        )
                    }
                }
            }.run {
                delay(1000L)
                withContext(Dispatchers.Main) {
                    uploadServiceArrivalDepartureAtpInfluencers()
                }
            }
        }
    }

    private fun uploadServiceArrivalDepartureAtpInfluencers() {
        val token = preference.getString("token")
        GlobalScope.launch {
            var data: InfluencersMarkArrivalDepartureAtpModel? = null
            withContext(Dispatchers.IO) {
                val currentHitTime = System.currentTimeMillis()
                data = appDatabase.markArrivalDepartureAtpInfluencersDao()
                    .getSingleArrivalDepartureData(0, currentHitTime, 2)
            }.run {
                withContext(Dispatchers.Main) {
                    data?.let {
                        multipleFileUploadWebserviceAtpInfluencers(it, token)
                    }
                }
            }
        }
    }

    private fun multipleFileUploadWebserviceAtpInfluencers(
        data: InfluencersMarkArrivalDepartureAtpModel,
        token: String
    ) {
        val map: MutableMap<String, RequestBody> = HashMap()
        map["apiToken"] = RequestBody.create(MediaType.parse("text/plain"), token)
        map["influencer_atp_id"] =
            RequestBody.create(MediaType.parse("text/plain"), (data.influencer_atp_id).toString())
        map["ticket_number"] =
            RequestBody.create(MediaType.parse("text/plain"), data.ticket_number ?: "")

        map["arrival_time"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_date ?: "")
        map["arrival_latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_lat ?: "")
        map["arrival_longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_lng ?: "")
        map["arrival_comment"] =
            RequestBody.create(MediaType.parse("text/plain"), data.arrival_comment ?: "")

        map["departure_time"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_date ?: "")
        map["departure_latitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_lat ?: "")
        map["departure_longitude"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_lng ?: "")
        map["departure_comment"] =
            RequestBody.create(MediaType.parse("text/plain"), data.departure_comment ?: "")

        map["duration"] = RequestBody.create(MediaType.parse("text/plain"), data.duration)

        val parts: ArrayList<MultipartBody.Part> = ArrayList()
        val arrivalFile = File(data.arrival_image ?: "")
        val departureFile = File(data.departure_image ?: "")
        try {
            val requestFile1: RequestBody =
                RequestBody.create(MediaType.parse("image/*"), arrivalFile)
            val part =
                MultipartBody.Part.createFormData(
                    "arrival_image",
                    arrivalFile.name,
                    requestFile1
                )
            parts.add(part)

            val requestFile2: RequestBody =
                RequestBody.create(MediaType.parse("image/*"), departureFile)
            val part2 =
                MultipartBody.Part.createFormData(
                    "departure_image",
                    departureFile.name,
                    requestFile2
                )
            parts.add(part2)
        } catch (e: JSONException) {
            e.printStackTrace()
        }

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase.markArrivalDepartureAtpInfluencersDao()
                    .updateArrivalDepartureData(data.influencer_atp_id, 1)
            }
        }

        val okHttpClient = RetrofitClient.httpClient
        val builder: Retrofit.Builder = Retrofit.Builder()
            .baseUrl(RetrofitClient.LIVE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
        val retrofit = builder.build()
        val client: ApiServiceRequest = retrofit.create(
            ApiServiceRequest::class.java
        )
        val call: Call<ArrivalDepartureResponse> = client.arrivalDepartureInfluencers(map, parts)
        call.enqueue(object : Callback<ArrivalDepartureResponse?> {
            override fun onResponse(
                call: Call<ArrivalDepartureResponse?>,
                response: Response<ArrivalDepartureResponse?>
            ) {
                if (response.body() != null) {
                    val model: ArrivalDepartureResponse? = response.body()
                    if (model != null) {
                        GlobalScope.launch {
                            if (model.status_code == 200) {
                                withContext(Dispatchers.IO) {
                                    data.let {
                                        appDatabase.markArrivalDepartureAtpInfluencersDao()
                                            .deleteTableData(
                                                it.influencer_atp_id
                                            )
                                    }
                                }.run {
                                    withContext(Dispatchers.Main) {
                                        uploadServiceArrivalDepartureAtpInfluencers()
                                    }
                                }
                            } else {
                                withContext(Dispatchers.IO) {
                                    val currentHitTimeTwoHour =
                                        System.currentTimeMillis() + (121 * 60 * 1000)
                                    appDatabase.markArrivalDepartureAtpInfluencersDao()
                                        .updateArrivalDeparture(
                                            data.influencer_atp_id,
                                            currentHitTimeTwoHour,
                                            0,
                                            data.ticket_number!!
                                        )
                                }.run {
                                    delay(1000)
                                    withContext(Dispatchers.Main) {
                                        uploadServiceArrivalDepartureAtpInfluencers()
                                    }
                                }
                            }
                        }
                    }
                }
            }

            override fun onFailure(call: Call<ArrivalDepartureResponse?>, t: Throwable) {
                GlobalScope.launch {
                    withContext(Dispatchers.Main) {
                        uploadServiceArrivalDepartureAtpInfluencers()
                    }.run {
                        withContext(Dispatchers.IO) {
                            val currentHitTimeTwoHour =
                                System.currentTimeMillis() + (121 * 60 * 1000)
                            appDatabase.markArrivalDepartureAtpInfluencersDao()
                                .updateArrivalDeparture(
                                    data.influencer_atp_id,
                                    currentHitTimeTwoHour,
                                    0,
                                    data.ticket_number!!
                                )
                        }
                    }

                }
            }
        })
    }

    fun companyFlDesignationDataInfluencers() {
        val token = preference.getString("token")
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = getLastHitTimeData("v3/getInfluencerTypeDesignationFL")
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_date"] = time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java)
                        .getInfluencersCompanyDesignationFl(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<InfluencersTypeDesignationFlModel>() {
                            override fun onCompleted() {

                            }

                            override fun onError(e: Throwable?) {

                            }

                            override fun onNext(it: InfluencersTypeDesignationFlModel?) {
                                getInfluencerListData()
                                getInfluencerHeadOfficeListData()
                                if (it != null) {
                                    if (it.status_code == 200) {
                                        insertInfluencerDataInDb(it.data)
                                    } else if (it.status_code == 409) {
                                        EventBus.getDefault().post("logout")
                                    }
                                }
                            }

                        })
                }
            }
        }
    }

    private fun insertInfluencerDataInDb(data: com.dhanuka.indhan.institutional.model.influencers.Data?) {

        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                "v3/getInfluencerTypeDesignationFL".updateTimeOnDb()
                data?.company_data?.let {
                    it.forEach { company ->
                        appDatabase.companyTypeDataListInfluencersDao().insert(company)
                    }
                }

                data?.designation_data?.let {
                    it.forEach { designation ->
                        appDatabase.designationDataListInfluencersDao().insert(designation)
                    }
                }

                data?.fl_data?.let {
                    it.forEach { fl ->
                        appDatabase.flDataListInfluencersDao().insert(fl)
                    }
                }

                data?.category_data?.let {
                    it.forEach { it1 ->
                        appDatabase.categoryDataInfluencersDao().insert(it1)
                    }
                }
            }
        }
    }

    private fun getInfluencerListData() {
        val token = preference.getString("token")
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = getLastHitTimeData("v3/InfluencerListing")
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_at"] = time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).getInfluencersList(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<InfluencersListingResponse>() {
                            override fun onCompleted() {

                            }

                            override fun onError(e: Throwable?) {

                            }

                            override fun onNext(it: InfluencersListingResponse?) {
                                if (it != null) {
                                    if (it.status_code == 200) {
                                        insertInfluencerDataInDb(it.data)
                                    } else if (it.status_code == 409) {
                                        EventBus.getDefault().post("logout")
                                    }
                                }
                            }

                        })
                }
            }
        }

    }

    private fun insertInfluencerDataInDb(data: List<InfluencersData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                "v3/InfluencerListing".updateTimeOnDb()
                data?.let {
                    it.forEach { teaData ->
                        appDatabase.influencersListDataDao().insert(teaData)
                    }
                }
            }
        }
    }

    private fun getInfluencerHeadOfficeListData() {
        val token = preference.getString("token")
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = getLastHitTimeData("v3/influencerListingHeadOfc")
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_at"] = time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).getInfluencersHeadOfficeList(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<InfluencersHeadOfficeListResponse>() {
                            override fun onCompleted() {

                            }

                            override fun onError(e: Throwable?) {

                            }

                            override fun onNext(it: InfluencersHeadOfficeListResponse?) {
                                if (it != null) {
                                    if (it.status_code == 200) {
                                        insertInfluencerHeadOfficeDataInDb(it.data)
                                    } else if (it.status_code == 409) {
                                        EventBus.getDefault().post("logout")
                                    }
                                }
                            }

                        })
                }
            }
        }

    }

    private fun insertInfluencerHeadOfficeDataInDb(data: List<InfluencersHeadOfficeData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                "v3/influencerListingHeadOfc".updateTimeOnDb()
                data?.let {
                    it.forEach { teaData ->
                        appDatabase.influencersHeadOfficeListDataDao().insert(teaData)
                    }
                }
            }
        }
    }

    private fun isFileLessThan2KB(file: File): Boolean {
        val maxFileSize = 2 * 1024
        val l = file.length()
        val fileSize = l.toString()
        val finalFileSize = fileSize.toInt()
        return finalFileSize >= maxFileSize
    }

}