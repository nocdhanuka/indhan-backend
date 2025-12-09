package com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration

import android.content.Intent
import android.location.Location
import android.os.Bundle
import android.os.Handler
import android.text.TextUtils
import android.view.View
import androidx.appcompat.app.AlertDialog
import com.dhanuka.indhan.ATPMenu.activity.models.CommonAtpResponse
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.adapter.IfcFarmerListAdapter
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.ActivityDurationModel
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.AddIfcFarmer
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.IfcFarmerTicket
import com.dhanuka.indhan.RegisterFarmerScreen.offlineFarmerData.OfflineDataIfcFarmer
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.AppLogFile.writeLineInFile
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.LogModel
import com.dhanuka.indhan.util.Utility
import com.google.firebase.analytics.FirebaseAnalytics
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_ifc_farmer.*
import kotlinx.coroutines.*
import org.joda.time.DateTime
import rx.Single
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*

class IfcFarmerRegistrationActivity : BaseActivity(), View.OnClickListener,
    IfcFarmerListAdapter.OnAction {

    private var firebaseAnalytics: FirebaseAnalytics? = null
    private var isTicketAvailable: Boolean = false
    private var message: String = "Farmer are less than 8, Are you sure you want to submit?"
    private lateinit var preference: Preference
    private lateinit var serverDateFormat: SimpleDateFormat
    private var appDatabase: AppDatabase? = null
    private val uniqueIfcFarmerRecordModel = LinkedList<AddIfcFarmer>()
    private lateinit var farmerLIstAdapter: IfcFarmerListAdapter
    private val uniqueListActivityDuration = ArrayList<ActivityDurationModel>()
    private var totalPda = 0.0
    private var ticketNumber = ""

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.ivAddMnNumber -> {
                if (isTicketAvailable) {
                    val intent = Intent(
                        applicationContext,
                        AddIfcFarmerRegistrationActivity::class.java
                    )
                    if (uniqueIfcFarmerRecordModel.size > 0) {
                        intent.putExtra("listOfData", Gson().toJson(uniqueIfcFarmerRecordModel))
                    }
                    intent.putExtra("ticket_no", ticketNumber)
                    startActivityForResult(intent, 101)
                } else {
                    makeToast("No Active Ticket found!")
                }
            }
//            R.id.btnRbSubmit -> {
//                val data = uniqueIfcFarmerRecordModel.firstOrNull { it.status == 0 }
//                if (data != null) {
//                    submitIfcFarmerData()
//                } else {
//                    makeToast("There is no new farmer, please add new farmer and submit.")
//                }
//            }
            R.id.ivBack -> {
                finish()
            }

            R.id.offlineData -> {
                val intent = Intent(
                    applicationContext,
                    OfflineDataIfcFarmer::class.java
                )
                if (isTicketAvailable) {
                    intent.putExtra("ticket_no", ticketNumber)
                } else {
                    makeToast("No Active Ticket found!")
                }
                startActivityForResult(intent, 201)
            }
        }
    }

//    private fun submitIfcFarmerData() {
//        totalPda = 0.0
//        val allDataToSync = ArrayList<AddIfcFarmer>()
//        uniqueIfcFarmerRecordModel.forEach() {
//            if (it.status == 0) {
//                allDataToSync.add(it)
//            }
//            totalPda += Utility.roundOffDecimal((it.acrers ?: "0").toDouble())
//        }
//        saveActivityDuration(
//            uniqueIfcFarmerRecordModel.first(),
//            uniqueIfcFarmerRecordModel.last(),
//            true
//        )
//        tvTotalAddedFarmer.text = "${uniqueIfcFarmerRecordModel.size} - Farmers Added"
//        tvTotalPda.text = "${Utility.roundOffDecimal(totalPda)} - PDA"
//        if (uniqueIfcFarmerRecordModel.size < 8) {
//            message = "This activity may not get qualified as number of farmers are less than 8."
//            alertDialog()
//        } else if (uniqueIfcFarmerRecordModel.size >= 10 && totalPda.roundToInt() < 50) {
//            message = "This activity may not get qualified as PDA is less than 50."
//            alertDialog()
//        } else {
//            uploadData()
//        }
//    }

    private fun saveActivityDuration(first: AddIfcFarmer, last: AddIfcFarmer, isUpload: Boolean) {
        serverDateFormat = SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
        val firstDateTime =
            if (first.status == 1) (first.image_date_time) else getLocalDateTime(first.image_date_time)
        val lastDatTime =
            if (last.status == 1) (last.image_date_time) else getLocalDateTime(last.image_date_time)

        val diff = Utility.findDifference(serverDateFormat, firstDateTime, lastDatTime)

        val finalDiff = 1 + diff
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase?.activityDurationDao()?.deleteTableData(ticketNumber)
            }
        }
        var distance = 0.0

        for ((index) in uniqueIfcFarmerRecordModel.withIndex()) {
            if (uniqueIfcFarmerRecordModel[index] == uniqueIfcFarmerRecordModel.first) {
                uniqueIfcFarmerRecordModel[index].setActivityDistance(0.0)
            } else {
                val disCal = distanceBetweenLatLng(
                    uniqueIfcFarmerRecordModel[index - 1].latitude.toFloat(),
                    uniqueIfcFarmerRecordModel[index - 1].longitude.toFloat(),
                    uniqueIfcFarmerRecordModel[index].latitude.toFloat(),
                    uniqueIfcFarmerRecordModel[index].longitude.toFloat(),
                ).toDouble()
                uniqueIfcFarmerRecordModel[index].setActivityDistance(disCal)
            }
        }

        for ((index) in uniqueIfcFarmerRecordModel.withIndex()) {
            GlobalScope.launch {
                val data = uniqueIfcFarmerRecordModel[index]
                withContext(Dispatchers.IO) {
                    appDatabase?.ifcFarmerDao()?.updateIfcFarmerUploadedData(
                        data.getActivityDistance(),
                        data.ticket_no,
                        data.mobile
                    )
                }.run {
                    withContext(Dispatchers.Main) {
                        distance += data.getActivityDistance()
                        val tempDistance = Utility.roundOffDecimal(distance / 1000)
                        tvTotalDistance.text = tempDistance.toString().plus(" Km Distance Covered")
                        val activity = ActivityDurationModel(
                            status = 0,
                            ticket_no = ticketNumber,
                            activity_duration = finalDiff,
                            total_pda = (Utility.roundOffDecimal(totalPda)).toString(),
                            total_activity_distance = Utility.roundOffDecimal(distance)
                        )
                        GlobalScope.launch {
                            withContext(Dispatchers.IO) {
                                appDatabase?.activityDurationDao()?.insert(activity)
                            }
                        }
                    }
                }
            }
        }
        GlobalScope.launch {
            delay(300)
            withContext(Dispatchers.Main) {
                if (isUpload || last.status == 0) {
                    OfflineDatabase(this@IfcFarmerRegistrationActivity).getUniqueActivityDuration(
                        ticketNumber
                    );
                }
            }
        }

    }

    private fun getLocalDateTime(imageDateTime: String?): String {
        val output: SimpleDateFormat
        val data = try {
            val sdf = SimpleDateFormat("yyyy-MM-dd hh:mm a")
            output = SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
            sdf.parse(imageDateTime!!)
        } catch (e: Exception) {
            return ""
        }
        return output.format(data!!)
    }

    private fun getTotalPDA() {
        totalPda = 0.0
        val allDataToSync = ArrayList<AddIfcFarmer>()
        uniqueIfcFarmerRecordModel.forEach() {
            if (it.status == 0) {
                allDataToSync.add(it)
            }
            totalPda += (it.acrers ?: "0").toDouble()
        }
        tvTotalPda.text = "${Utility.roundOffDecimal(totalPda)} - PDA"

        tvTotalAddedFarmer.text = "${uniqueIfcFarmerRecordModel.size} - Farmers Added"
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_ifc_farmer)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        firebaseAnalytics = FirebaseAnalytics.getInstance(this)
        preference = Preference(this)
        ivAddMnNumber.setOnClickListener(this)
        btnRbSubmit.setOnClickListener(this)
        offlineData.setOnClickListener(this)
        ivBack.setOnClickListener(this)
//        changeStatus()
        setAdapter()
        getDataForActiveTicket(false)
    }

    private fun changeStatus() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDatabase?.ifcFarmerDao()?.updateIfcFarmerUploadedData(0, "PV24271310")
            }
        }
    }

    private fun setAdapter() {
        farmerLIstAdapter = IfcFarmerListAdapter(
            uniqueIfcFarmerRecordModel,
            this,
            this@IfcFarmerRegistrationActivity
        )
        rvManualAdded.adapter = farmerLIstAdapter
    }

    private fun getDataForActiveTicket(isUpload: Boolean) {
        if (CheckConnectionStatus.isInternetAvailable(this@IfcFarmerRegistrationActivity)) {
            showProgress()
            val token = preference.getString("token")
            RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).ifcFarmerGetTicket(token)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<IfcFarmerTicket>() {
                    override fun onCompleted() {
                        hideProgress()
                    }

                    override fun onError(e: Throwable) {
                        hideProgress()
                    }

                    override fun onNext(ticketData: IfcFarmerTicket) {
                        hideProgress()
                        setTicketData(ticketData, isUpload)
                    }
                })
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
            getAllTicketListFromDB()
        }
    }

    private fun setTicketData(ticketData: IfcFarmerTicket, isUpload: Boolean) {
        if (ticketData.data.isNotEmpty()) {
            if (!TextUtils.isEmpty(ticketData.data[0].ticket_no)) {
                isTicketAvailable = true
                ticketNumber = ticketData.data[0].ticket_no
                tvReTicketNoDisplay.text = ticketData.data[0].ticket_no
                tvMeTicketTime.text = ticketData.data[0].datetime_format
            }
        } else {
            isTicketAvailable = false
        }

        ticketData.ifc_farmer_data?.forEach() {
            val farmer = AddIfcFarmer()
            farmer.farmer_name = it.farmer_name ?: ""
            farmer.serverId = it.id
            farmer.latitude = (it.latitude ?: "0.0").toDouble()
            farmer.longitude = (it.longitude ?: "0.0").toDouble()
            farmer.acrers = it.acreage ?: "0"
            farmer.mobile = it.mobile ?: ""
            farmer.image_date_time = it.image_date_time ?: ""
            farmer.serverTime = it.updated_at ?: ""
            farmer.status = 1
            farmer.setActivityDistance((it.activity_distance ?: "0.0").toDouble())
            farmer.uploaded = true
            farmer.imageName = "server"
            farmer.image =
                if (!TextUtils.isEmpty(it.image)) ticketData.image_path.plus(it.image) else ""
            farmer.ticket_no = it.ticket_no ?: ticketNumber
            uniqueIfcFarmerRecordModel.add(farmer)
            insetFarmerInDataBase(farmer)
            farmerLIstAdapter.notifyDataSetChanged()
        }

        getTotalPDA()
        if (!TextUtils.isEmpty(ticketNumber)) {
            Handler().postDelayed({
                getDataFromDataBase()
                getUniqueActivityDuration(isUpload)
            }, 1000)
        }
    }

    private fun insetFarmerInDataBase(farmer: AddIfcFarmer) {
        Single.fromCallable {
            appDatabase?.ifcFarmerDao()?.insert(farmer)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
        }.subscribe()
    }

    private fun getDataFromDataBase() {
        rx.Observable.fromCallable {
            appDatabase?.ifcFarmerDao()
                ?.getAllIfcFarmerDataForTicket(ticketNumber, false) as ArrayList<AddIfcFarmer>
        }.subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread()).subscribe {
                if (it != null) {
                    uniqueIfcFarmerRecordModel.addAll(
                        it as Collection<AddIfcFarmer>
                    )
                    getTotalPDA()
                    farmerLIstAdapter.notifyDataSetChanged()
                }
            }
    }

    private fun getAllTicketListFromDB() {
        rx.Observable.fromCallable {
            appDatabase?.ticketHistoryDao()?.allTicketHistory as List<TicketHistoryModel.Datum>
        }.subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread()).subscribe {
                if (it != null) {
                    uniqueIfcFarmerRecordModel.clear()
                    for (i in it.indices) {
                        val datum = it[i]
                        if (datum.ticketStatus.equals("ACTIVE", ignoreCase = true)
                            && datum.dataSource == "IFC"
                        ) {
                            setTicketDataOffline(datum)
                            break
                        }
                    }
                }
            }
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 101 && resultCode == 101) {
            if (data!!.extras != null && data.getSerializableExtra("UNIQUE_NUMBERS_RECORDS") != null) {
                val dataValue =
                    data.extras!!.getSerializable("UNIQUE_NUMBERS_RECORDS") as ArrayList<AddIfcFarmer>
                uniqueIfcFarmerRecordModel.addAll(dataValue)
                farmerLIstAdapter.notifyDataSetChanged()
                getUniqueActivityDuration(true)
                getTotalPDA()
            }
        } else if (requestCode == 201 && resultCode == 201) {
            uniqueIfcFarmerRecordModel.clear()
            getDataForActiveTicket(true)
        }
    }

    private fun getUniqueActivityDuration(isUpload: Boolean) {
        GlobalScope.launch {
            var list = ArrayList<ActivityDurationModel>()
            withContext(Dispatchers.IO) {
                list = appDatabase?.activityDurationDao()?.getAllActivityDurationData(
                    status = 0,
                    ticketNumber = ticketNumber
                ) as ArrayList<ActivityDurationModel>
            }.run {
                withContext(Dispatchers.Main) {
                    if (list != null && list.isNotEmpty()) {
                        uniqueListActivityDuration.clear()
                        uniqueListActivityDuration.addAll(list)
                    }
                    if (uniqueIfcFarmerRecordModel.size > 0) {
                        saveActivityDuration(
                            uniqueIfcFarmerRecordModel.first,
                            uniqueIfcFarmerRecordModel.last,
                            isUpload
                        )
                    }
                }
            }
        }
    }

//    private fun alertDialog() {
//        AlertDialog.Builder(this).setTitle("Register IFC Farmer")
//            .setMessage(message)
//            .setCancelable(false)
//            .setPositiveButton(
//                "Got It"
//            ) { _, _ -> uploadData() }.show()
//    }
//
//    private fun uploadData() {
//        Single.fromCallable {
//            val data = appDatabase?.ifcFarmerDao()?.getSingleIfcFarmerData(
//                status = 0,
//                isuploaded = false
//            )
//            val allData = appDatabase?.ifcFarmerDao()?.getAllIfcFarmerDataList()
//            if (data != null) {
//                runOnUiThread {
//                    if (CheckConnectionStatus.isInternetAvailable(this)) {
//                        logEventAddFarmers("", allData)
//                        (this as BaseActivity).isNetworkAvailable(this@IfcFarmerRegistrationActivity)
//                        Handler(Looper.getMainLooper()).postDelayed({
//                            makeToast("Your data will be uploaded in background. Please check after sometime.")
//                            finish()
//                        }, 800)
//                    } else {
//                        logEventAddFarmers("No Internet : ", allData)
//                        makeToast("Internet connection not available. Hence data is saved offline and gets sync when you are back online.")
//                    }
//                }
//            } else {
//                logEventAddFarmers("No Data in local with status 0: ", allData)
//                hideProgress()
//            }
//        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
//        }.subscribe()
//    }

    override fun onDeleteClick(pos: Int, status: Int) {
        AlertDialog.Builder(this).setTitle("Register IFC Farmer")
            .setMessage("Are sure you want to delete farmer.")
            .setPositiveButton(
                "Yes"
            ) { _, _ -> deleteFarmer(pos, status) }.setNegativeButton(
                "No"
            ) { dialogInterface, _ -> dialogInterface.dismiss() }.show()
    }

    private fun deleteFarmer(pos: Int, status: Int) {
        if (status == 1) {
            logEventCustom("delete from api", status)
            callApiDeleteData(
                uniqueIfcFarmerRecordModel[pos].serverId,
                uniqueIfcFarmerRecordModel[pos].ticket_no,
                uniqueIfcFarmerRecordModel[pos].mobile,
                uniqueIfcFarmerRecordModel[pos].id
            )
        } else {
            logEventCustom("delete from local", status)
            deleteLocalData(
                uniqueIfcFarmerRecordModel[pos].serverId,
            )
        }
    }

    private fun logEventCustom(s: String, status: Int) {
        val bundle = Bundle()
        bundle.putString(
            "user_detail", preference.getString("user_id")
                .plus("_${preference.getString("ecn")}")
                .plus("_${preference.getString("user_type")}")
                .plus(
                    "_${preference.getString("Username")}"
                        .plus("_${preference.getString("designation")}")
                )
        )
        bundle.putString(
            FirebaseAnalytics.Param.CONTENT_TYPE,
            if (status == 1) "deleted_ifc_farmer_server" else "ifc_delete_local"
        )
        bundle.putString("delete_local", s)
        bundle.putString(FirebaseAnalytics.Param.ITEM_ID, preference.getString("user_id"))
        bundle.putString(
            FirebaseAnalytics.Param.ITEM_NAME,
            if (status == 1) "deleted_ifc_farmer_server" else "deleted_ifc_farmer_local"
        )
        bundle.putString("uploaded_from", "ifc farmer list")
        firebaseAnalytics?.logEvent("DELETE_FARMER", bundle)

        val model = LogModel()
        model.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
        val data =
            ("Ticket No : $ticketNumber |" +
                    " ${if (status == 1) "deleted_ifc_farmer_server" else "ifc_delete_local"} " +
                    "delete from : ${if (status == 1) "server" else "local"}").trimIndent()
        model.apiData = data
        if (status == 1) {
            model.apiName = "Api Name : v3/delifcfarmer"
        } else {
            model.apiName = "Api Name : Delete from local"
        }

        val userData = (preference.getString("user_id") + "_"
                + preference.getString("ecn") + "_"
                + preference.getString("user_type") + "_"
                + preference.getString("Username") + "_"
                + preference.getString("designation"))
        model.userData = "\nUSER DATA : -> $userData"

        writeLineInFile(Gson().toJson(model), preference.getString("ecn"))
    }

    private fun deleteLocalData(status: Int) {
        rx.Observable.fromCallable {
            appDatabase?.ifcFarmerDao()
                ?.deleteTableData(
                    status
                )
        }.subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread()).subscribe {
                uniqueIfcFarmerRecordModel.clear()
                getDataForActiveTicket(true)
            }
    }

    private fun callApiDeleteData(serverId: Int, ticketNo: String?, mobile: String?, id: Int) {
        showProgress()
        val request = HashMap<String, Any>()
        val token = preference.getString("token")
        request["token"] = token
        request["farmer_ids"] = serverId.toString()
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).removeFarmerFromApi(request)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonAtpResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(ticketData: CommonAtpResponse) {
                    hideProgress()
                    makeToast(ticketData.message)
                    uniqueIfcFarmerRecordModel.clear()
                    deleteLocalData(serverId)
                }
            })
    }

    private fun setTicketDataOffline(ticketData: TicketHistoryModel.Datum) {
        if (ticketData.ticket.isNotEmpty()) {
            if (!TextUtils.isEmpty(ticketData.ticket)) {
                isTicketAvailable = true
                ticketNumber = ticketData.ticket
                tvReTicketNoDisplay.text = ticketData.ticket
                tvMeTicketTime.text = ticketData.datetimeFormat
            }
        } else {
            isTicketAvailable = false
        }
        if (!TextUtils.isEmpty(ticketNumber)) {
            Handler().postDelayed({
                getDataFromOfflineDataBase()
                getUniqueActivityDuration(false)
            }, 1000)
        }
    }

    private fun getDataFromOfflineDataBase() {
        rx.Observable.fromCallable {
            appDatabase?.ifcFarmerDao()
                ?.getAllIfcFarmerDataForTicket(ticketNumber) as ArrayList<AddIfcFarmer>
        }.subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread()).subscribe {
                if (it != null) {
                    uniqueIfcFarmerRecordModel.addAll(it as Collection<AddIfcFarmer>)
                    getTotalPDA()
                    farmerLIstAdapter.notifyDataSetChanged()
                }
            }
    }

    private fun distanceBetweenLatLng(
        lat_a: Float,
        lng_a: Float,
        lat_b: Float,
        lng_b: Float
    ): Float {
        val startPoint = Location("locationA")
        startPoint.latitude = lat_a.toDouble()
        startPoint.longitude = lng_a.toDouble()
        val endPoint = Location("locationB")
        endPoint.latitude = lat_b.toDouble()
        endPoint.longitude = lng_b.toDouble()
        val distance: Double = startPoint.distanceTo(endPoint).toDouble()
        return distance.toFloat()
    }

    private fun logEventAddFarmers(s: String, dataList: List<AddIfcFarmer?>?) {
        val model = LogModel()
        model.timeToWrite = "Date Time : ${DateTime.now()}".trimIndent()
        val data =
            "Ticket No : $ticketNumber | Ifc Farmers Local Data -> ${Gson().toJson(dataList)} Ifc Farmers Count : ${dataList?.size ?: 0}".trimIndent()
        model.apiData = data
        model.apiName =
            "Api Name : v3/addIfcFarmer | Message ${if (TextUtils.isEmpty(s)) "Api Called" else s}"
        val userData = (preference.getString("user_id") + "_"
                + preference.getString("ecn") + "_"
                + preference.getString("user_type") + "_"
                + preference.getString("Username") + "_"
                + preference.getString("designation"))
        model.userData = "\nUSER DATA : -> $userData"
        writeLineInFile(Gson().toJson(model), preference.getString("ecn"))

        val bundle = HashMap<String, Any>()
        bundle[FirebaseAnalytics.Param.ITEM_ID] = preference.getString("user_id")
        bundle["Api_Name"] = "v3/addIfcFarmer"
        bundle["api_data"] = data
        bundle["user_detail"] = userData
        if (s.contains("Error")) {
            bundle["error_message"] = s
        } else {
            bundle["message"] = s
        }
        Utility.logEvent("IfcFarmerRegistrationActivity", "add_ifc_farmer", bundle)
    }
}