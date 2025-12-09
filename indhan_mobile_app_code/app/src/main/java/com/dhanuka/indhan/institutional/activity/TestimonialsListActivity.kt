package com.dhanuka.indhan.institutional.activity

import android.app.Dialog
import android.content.BroadcastReceiver
import android.content.Context
import android.content.Intent
import android.content.IntentFilter
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import android.view.Window
import androidx.localbroadcastmanager.content.LocalBroadcastManager
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Constants.Constants.IMAGEBASEURL
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.Testimonial.service.ServiceConstrants
import com.dhanuka.indhan.VideoPlayer.VideoPlayActivity
import com.dhanuka.indhan.institutional.adapter.teaEstate.TestimonialAdapterTeaEstate
import com.dhanuka.indhan.institutional.model.RefreshEventModel
import com.dhanuka.indhan.institutional.model.TestimonialListData
import com.dhanuka.indhan.institutional.model.TestimonialListResponse
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken
import kotlinx.android.synthetic.main.activity_intitutional_testimonial.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*

class TestimonialsListActivity : BaseActivity(), TestimonialAdapterTeaEstate.OnClickItem,
    SwipeRefreshLayout.OnRefreshListener {
    private var preference: Preference? = null
    private var appDatabase: AppDatabase? = null
    var testimonialAdapterTeaEstate: TestimonialAdapterTeaEstate? = null
    var listOfData: ArrayList<TestimonialListData> = ArrayList()
    private var parentId: String = ""
    private var type: String = ""
    private var fromCircular = false

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.imgFilter -> {
                val intent = Intent(
                    this@TestimonialsListActivity,
                    TeaEstateTestimonialFilterActivity::class.java
                )
                startActivityForResult(intent, 220)
            }
            R.id.imgAddTestimonial -> {
                launchIntent(AddNewTestimonialActivity::class.java, false)
            }
            R.id.ivBack -> {
                EventBus.getDefault().post(RefreshEventModel(data = 1, page = "tea_estate"))
                finish()
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_intitutional_testimonial)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        intent?.getStringExtra("parentId").let {
            parentId = it ?: ""
            type = intent?.getStringExtra("type") ?: "0"
            if(it != null){
                imgAddTestimonial.visibility = View.GONE
                imgFilter.visibility = View.GONE
            }
        }
        intent?.getStringExtra("from_circular")?.let {
            imgAddTestimonial.visibility = View.GONE
            imgFilter.visibility = View.GONE
            fromCircular = true
        }
        if (!fromCircular) {
            setListeners()
            setAdapter()
            getTestimonialList(false)
        } else {
            ivBack.setOnClickListener(this)
            listOfData = Gson().fromJson(
                intent?.getStringExtra("data")!!,
                object : TypeToken<ArrayList<TestimonialListData?>?>() {}.type
            ) as ArrayList<TestimonialListData>
            setAdapter()
        }
    }

    private fun setListeners() {
        imgFilter.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        imgAddTestimonial.setOnClickListener(this)
        swipeRefreshLayout.setOnRefreshListener(this)
    }

    private fun setAdapter() {
        testimonialAdapterTeaEstate =
            TestimonialAdapterTeaEstate(this@TestimonialsListActivity, listOfData, fromCircular)
        rvTestimonialList.adapter = testimonialAdapterTeaEstate
        testimonialAdapterTeaEstate?.setClickListener(this)
        if (listOfData.size > 0) {
            rvTestimonialList.visibility = View.VISIBLE
            tvNoData.visibility = View.GONE
        } else {
            rvTestimonialList.visibility = View.GONE
            tvNoData.visibility = View.VISIBLE
        }
    }

    override fun onClickView(view: View, subMenu: TestimonialListData) {
        val intent = Intent(this@TestimonialsListActivity, VideoPlayActivity::class.java)
        intent.putExtra("VideoUrl", IMAGEBASEURL.plus(subMenu.image_video))
        startActivity(intent)
    }

    override fun onClickViewCircular(view: View, subMenu: TestimonialListData) {
        val intent = Intent()
        intent.putExtra("position", this.intent?.getIntExtra("position", 0))
        intent.putExtra("data", Gson().toJson(subMenu))
        setResult(RESULT_OK, intent)
        finish()
    }

    private fun getTestimonialList(b: Boolean) {
        if (CheckConnectionStatus.isInternetAvailable(this@TestimonialsListActivity)) {
            swipeRefreshLayout.isRefreshing = false
            showProgress()
            GlobalScope.launch {
                var timeData: ApiTimeTableModel? = null
                withContext(Dispatchers.IO) {
                    timeData = "v3/testimonialListInstitutional".getLastHitTimeData()
                }.run {
                    withContext(Dispatchers.Main) {
                        val time = "${timeData?.updatedAt ?: 0}"
                        RetrofitClient.mvpClient
                            .create(ApiServiceRequest::class.java)
                            .getTestimonialList(if (b) "0" else time)
                            .subscribeOn(Schedulers.newThread())
                            .observeOn(AndroidSchedulers.mainThread())
                            .subscribe(object : Subscriber<TestimonialListResponse>() {
                                override fun onCompleted() {
                                    hideProgress()
                                }

                                override fun onError(e: Throwable?) {
                                    hideProgress()
                                }

                                override fun onNext(it: TestimonialListResponse?) {
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
        } else {
            getLocalData()
        }
    }

    private fun insertDataInDb(data: List<TestimonialListData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data?.let {
                    it.forEach() { teaEstate ->
                        appDatabase?.testimonialListDataDao()?.insert(teaEstate)
                    }
                }
                "v3/testimonialListInstitutional".updateTimeOnDb()
                delay(300)
            }.run {
                withContext(Dispatchers.Main) {
                    getLocalData()
                    hideProgress()
                }
            }
        }
    }

    private fun getLocalData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                if (!TextUtils.isEmpty(parentId)) {
                    listOfData = (appDatabase?.testimonialListDataDao()
                        ?.allTestimonialDataByInstitutionalId(parentId.toInt(), type)
                        ?: ArrayList<TestimonialListData>()) as ArrayList<TestimonialListData>
                } else {
                    val listOfIds = ArrayList<Int>()
                    appDatabase?.teaEstateStgListDataDao()?.allTeaEstate?.forEach() {
                        it?.let { id ->
                            listOfIds.add(id.tea_estate_id)
                        }
                    }

                    appDatabase?.sugarMillListDataDao()?.allSugarMill?.forEach() {
                        it?.let { id ->
                            listOfIds.add(id.sugarmill_id)
                        }
                    }

                    appDatabase?.influencersListDataDao()?.allInfluencers?.forEach() {
                        it?.let { id ->
                            listOfIds.add(id.influencer_id)
                        }
                    }

                    delay(300)
                    listOfData.clear()
                    listOfIds.forEach() {
                        listOfData.addAll(
                            (appDatabase?.testimonialListDataDao()
                                ?.allTestimonialDataByInstitutionalId(it)
                                ?: ArrayList<TestimonialListData>()) as ArrayList<TestimonialListData>
                        )

                    }
                }
            }.run {
                withContext(Dispatchers.Main) {
                    val copyList = ArrayList(listOfData)
                    listOfData.clear()
                    listOfData =
                        copyList.distinctBy { it.institutional_testimonial_id } as ArrayList<TestimonialListData>
                    setAdapter()
                }
            }
        }
    }

    private fun String.getLastHitTimeData(): ApiTimeTableModel? {
        return appDatabase?.apiTimeDao()?.getApiTimeData(this)
    }

    private fun String.updateTimeOnDb() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val timeData = this@updateTimeOnDb.getLastHitTimeData()
                val sdf = SimpleDateFormat(Constants.DATE_TIME_FORMAT)
                val currentDate = sdf.format(Date())
                if (timeData != null) {
                    appDatabase?.apiTimeDao()?.updateTimeInDb(this@updateTimeOnDb, currentDate)
                } else {
                    val timeDataModel = ApiTimeTableModel()
                    timeDataModel.apiName = this@updateTimeOnDb
                    timeDataModel.updatedAt = currentDate
                    appDatabase?.apiTimeDao()?.insert(timeDataModel)
                }
            }
        }
    }

    private fun hideUploadingProgress() {
        if (lnrUploadingDetails.visibility == View.VISIBLE) {
            lnrUploadingDetails.visibility = View.GONE
        }
        progressBar.progress = 0
        setProgressUploadingName("")
        txtPercentage.text = "0 %"
    }

    private fun setProgressUploading(progressInt: Long) {
        if (lnrUploadingDetails.visibility == View.GONE) {
            lnrUploadingDetails.visibility = View.VISIBLE
        }
        progressBar.progress = progressInt.toInt()
        "$progressInt %".also { txtPercentage.text = it }
    }

    private fun setProgressUploadingName(name: String) {
        txtFileName.text = name
    }

    private fun showDialog(message: String) {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_ticket_number)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val ticketNumber = dialog.findViewById<View>(R.id.tvMeTicketNumber) as Medium
        val msg = dialog.findViewById<View>(R.id.msg) as Medium
        val ticBanner = dialog.findViewById<View>(R.id.tic_banner) as Medium
        ticketNumber.visibility = View.GONE
        ticBanner.visibility = View.GONE
        msg.text = message
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
            getTestimonialList(true)
            Preference.getInstance(this).putBolean(ServiceConstrants.TESTIMONIAL_UPLOADED, false)
        }
        dialog.show()
    }

    val broadcastReceiver: BroadcastReceiver = object : BroadcastReceiver() {
        override fun onReceive(context: Context?, intent: Intent?) {
            if (intent != null) {
                if (intent.hasExtra(ServiceConstrants.UPLOADING_DONE)) {
                    hideUploadingProgress()
                    showDialog("Testimonial Successfully Uploaded")
                }

                if (intent.hasExtra(ServiceConstrants.UPLOADING_FAILURE)) {
                    hideUploadingProgress()
                    showDialog("Error while uploading Testimonial, Max File size should be of 50 MB.")
                }

                if (intent.hasExtra(ServiceConstrants.UPLOADING_PROGRESS)) {
                    setProgressUploading(
                        intent.getLongExtra(
                            ServiceConstrants.UPLOADING_PROGRESS,
                            0
                        )
                    )
                }
                if (intent.hasExtra(ServiceConstrants.UPLOADING_PRODUCT_NAME)) {
                    setProgressUploadingName(
                        intent.getStringExtra(ServiceConstrants.UPLOADING_PRODUCT_NAME) ?: ""
                    )
                }
            }
        }
    }

    override fun onStart() {
        super.onStart()
        LocalBroadcastManager.getInstance(this).registerReceiver(
            broadcastReceiver,
            IntentFilter(ServiceConstrants.UPLOAD_VIDEO_INTENT_FILETER)
        )
    }

    override fun onPause() {
        super.onPause()
        LocalBroadcastManager.getInstance(this).unregisterReceiver(broadcastReceiver)
    }

    override fun onRefresh() {
        swipeRefreshLayout.isRefreshing = true
        getTestimonialList(true)
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (resultCode == RESULT_OK) {
            if (requestCode == 220) {
                val type = data?.getIntExtra("tea_estate_type", 0) ?: -1
                val teaId = data?.getStringExtra("tea_estate_id") ?: ""
                val sugarId = data?.getStringExtra("sugarmill_id") ?: ""
                val influencerId = data?.getStringExtra("influencer_id") ?: ""
                if (TextUtils.isEmpty(teaId) && type == -1 && TextUtils.isEmpty(sugarId) && TextUtils.isEmpty(
                        influencerId
                    )
                ) {
                    getLocalData()
                } else {
                    getLocalDataFilter(teaId, type, sugarId, influencerId)
                }
            }
        }
    }

    private fun getLocalDataFilter(
        teaId: String,
        type: Int,
        sugarId: String,
        influencerId: String
    ) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                if (!TextUtils.isEmpty(teaId)) {
                    listOfData = (appDatabase?.testimonialListDataDao()
                        ?.allTestimonialData(
                            teaId.toInt(),
                            "1"
                        )
                        ?: ArrayList<TestimonialListData>()) as ArrayList<TestimonialListData>
                } else if (!TextUtils.isEmpty(sugarId)) {
                    listOfData = (appDatabase?.testimonialListDataDao()
                        ?.allTestimonialData(sugarId.toInt(), "2")
                        ?: ArrayList<TestimonialListData>()) as ArrayList<TestimonialListData>
                } else if (!TextUtils.isEmpty(influencerId)) {
                    listOfData = (appDatabase?.testimonialListDataDao()
                        ?.allTestimonialData(
                            influencerId.toInt(),
                            "3"
                        )
                        ?: ArrayList<TestimonialListData>()) as ArrayList<TestimonialListData>
                } else if (type != -1) {
                    listOfData = if (type == 3) {
                        (appDatabase?.testimonialListDataDao()
                            ?.allTestimonialDataByType("2")
                            ?: ArrayList<TestimonialListData>()) as ArrayList<TestimonialListData>
                    } else {
                        (appDatabase?.testimonialListDataDao()
                            ?.allTestimonialDataType(type.toString())
                            ?: ArrayList<TestimonialListData>()) as ArrayList<TestimonialListData>
                    }
                } else {
                    getLocalData()
                }
            }.run {
                withContext(Dispatchers.Main) {
                    setAdapter()
                }
            }
        }
    }
}