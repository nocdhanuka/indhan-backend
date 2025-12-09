package com.dhanuka.indhan.DemoRequestEvaluated.activities

import android.content.Intent
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import androidx.activity.result.ActivityResult
import androidx.activity.result.ActivityResultCallback
import androidx.activity.result.contract.ActivityResultContracts
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.RequestMaterialAdapter
import com.dhanuka.indhan.DemoRequestEvaluated.models.RequestedMaterialResponse
import com.dhanuka.indhan.DemoRequestEvaluated.models.RequestedMaterialResponseData
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility.isDateInBetweenIncludingEndPoints
import com.google.gson.Gson
import kotlinx.android.synthetic.main.my_demo_request_activity.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*
import kotlin.collections.ArrayList

class MyDemoRequestActivity : BaseActivity(), SwipeRefreshLayout.OnRefreshListener,
    RequestMaterialAdapter.RequestedMaterialClick {
    var requestedMaterialAdapter: RequestMaterialAdapter? = null
    private lateinit var preference: Preference
    private var appDataBaseClient: AppDatabase? = null
    private var requestedDataList: ArrayList<RequestedMaterialResponseData> = ArrayList()
    private var allDataList: ArrayList<RequestedMaterialResponseData> = ArrayList()
    private var userType: String = ""
    private var product_id = 0
    private var product = ""
    private var crop_id = 0
    private var crop = ""
    private var fromDateNeeded: String = ""
    private var toDateNeeded: String = ""
    private var goDown_id = 0
    private var goDownName = ""
    private var fromDateRequested: String = ""
    private var toDateRequested: String = ""

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.imgFilter -> {
                val intent = Intent(this, FilterDemoRequestActivity::class.java)
                val b = Bundle()
                b.putString("crop_name", crop)
                b.putString("product_name", product)
                b.putString("needed_start", fromDateNeeded)
                b.putString("needed_end", toDateNeeded)
                b.putString("godown_name", goDownName)
                b.putString("requested_from", fromDateRequested)
                b.putString("requested_to", toDateRequested)
                b.putInt("crop_id", crop_id)
                b.putInt("product_id", product_id)
                b.putInt("godown_id", goDown_id)
                intent.putExtra("bundle", b)
                someActivityResultLauncher.launch(intent)
            }
            R.id.imgAddDemo -> {
                launchIntent(DemoRequestMaterialsActivity::class.java, false)
            }
            R.id.swipeRefreshLayout -> swipeRefreshLayout.post {
                swipeRefreshLayout.isRefreshing = true
                getDataForRequestedMaterial("1")
            }
        }
    }

    var someActivityResultLauncher = registerForActivityResult(
        ActivityResultContracts.StartActivityForResult(),
        ActivityResultCallback { result: ActivityResult ->
            if (result.resultCode == RESULT_OK) {
                applyFilterToActivity(result.data)
            }
        } as ActivityResultCallback<ActivityResult>)

    private fun applyFilterToActivity(data: Intent?) {
        data?.getBundleExtra("bundle")?.let { it ->
            crop = it.getString("crop_name") ?: ""
            product = it.getString("product_name") ?: ""
            fromDateNeeded = it.getString("needed_start") ?: ""
            toDateNeeded = it.getString("needed_end") ?: ""
            goDownName = it.getString("godown_name") ?: ""
            fromDateRequested = it.getString("requested_from") ?: ""
            toDateRequested = it.getString("requested_to") ?: ""
            crop_id = it.getInt("crop_id", 0)
            product_id = it.getInt("product_id", 0)
            goDown_id = it.getInt("godown_id", 0)
            requestedDataList.clear()
            if (crop_id != 0 || product_id != 0 || !TextUtils.isEmpty(fromDateNeeded)
                || !TextUtils.isEmpty(toDateNeeded) || goDown_id != 0
                || !TextUtils.isEmpty(fromDateRequested) || !TextUtils.isEmpty(toDateRequested)
            ) {
                allDataList.forEach() {
                    if (it.crop_name.equals(crop, ignoreCase = true)
                        || it.product_name.equals(product, ignoreCase = true)
                        || it.godown_name.equals(goDownName, ignoreCase = true)
                        || it.crop_id == crop_id
                        || it.product_id == product_id
                        || it.godown_id == goDown_id
                        || isDateInBetweenIncludingEndPoints(
                            fromDateNeeded,
                            toDateNeeded,
                            it.need_by_date ?: ""
                        )
                        || isDateInBetweenIncludingEndPoints(
                            fromDateRequested,
                            toDateRequested,
                            it.created_at ?: ""
                        )
                    ) {
                        requestedDataList.add(it)
                    }
                }
                GlobalScope.launch {
                    delay(300)
                    withContext(Dispatchers.Main) {
                        if (requestedDataList.size > 0) {

                        } else {
                            makeToast("No matched data found.")
                        }
                        setAdapter()
                    }
                }
            } else {
                GlobalScope.launch {
                    delay(300)
                    withContext(Dispatchers.Main) {
                        requestedDataList.addAll(allDataList)
                        setAdapter()
                    }
                }
            }


        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.my_demo_request_activity)
        preference = Preference(this)
        appDataBaseClient = DatabaseClient.getInstance(this).appDatabase
        userType = intent?.getStringExtra("user_type") ?: ""

        if (userType.equals("area", ignoreCase = true)
            || userType.equals("sales_area", ignoreCase = true)
        ) {
            imgAddDemo.visibility = View.VISIBLE
        } else {
            imgAddDemo.visibility = View.GONE
        }

        setAdapter()
        getDataForRequestedMaterial("0")
        setListeners()
    }

    private fun getLastHitTimeData(apiName: String): ApiTimeTableModel? {
        return appDataBaseClient?.apiTimeDao()?.getApiTimeData(apiName)
    }

    private fun String.updateTimeOnDb() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                val timeData = getLastHitTimeData(this@updateTimeOnDb)
                val sdf = SimpleDateFormat(Constants.DATE_TIME_FORMAT)
                val currentDate = sdf.format(Date())
                if (timeData != null) {
                    appDataBaseClient?.apiTimeDao()
                        ?.updateTimeInDb(this@updateTimeOnDb, currentDate)
                } else {
                    val timeDataModel = ApiTimeTableModel()
                    timeDataModel.apiName = this@updateTimeOnDb
                    timeDataModel.updatedAt = currentDate
                    appDataBaseClient?.apiTimeDao()?.insert(timeDataModel)
                }
            }
        }
    }

    private fun getDataForRequestedMaterial(from: String) {
        if (CheckConnectionStatus.isInternetAvailable(this@MyDemoRequestActivity)) {
            showProgress()
            var time = "0"
            GlobalScope.launch {
                withContext(Dispatchers.IO) {
                    getLastHitTimeData("v3/listrequestedMaterial")?.let {
                        time = it.updatedAt ?: "0"
                    }
                }.run {
                    withContext(Dispatchers.Main) {
                        val hashMap: HashMap<String, Any> = HashMap()
                        hashMap["apiToken"] = preference.getString("token")
                        hashMap["updated_date"] = if (from == "0") time else "0"
                        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
                            .demoMaterialRquestList(hashMap).subscribeOn(Schedulers.newThread())
                            .observeOn(AndroidSchedulers.mainThread())
                            .subscribe(object : Subscriber<RequestedMaterialResponse>() {
                                override fun onCompleted() {
                                    swipeRefreshLayout.isRefreshing = false
                                    hideProgress()
                                }

                                override fun onError(e: Throwable?) {
                                    swipeRefreshLayout.isRefreshing = false
                                    hideProgress()
                                }

                                override fun onNext(it: RequestedMaterialResponse?) {
                                    hideProgress()
                                    it?.let {
                                        if (it.status_code == 200) {
                                            insertMaterialDataInDb(it.data)
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
            hideProgress()
            getDataFromDB()
        }
    }

    private fun getDataFromDB() {
        swipeRefreshLayout.isRefreshing = false
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                requestedDataList = (appDataBaseClient?.requestedMaterialListDao()
                    ?.getAllMaterialRequestedData()
                    ?: ArrayList<RequestedMaterialResponseData>()) as ArrayList<RequestedMaterialResponseData>
                allDataList.clear()
                allDataList.addAll(ArrayList<RequestedMaterialResponseData>(requestedDataList))
            }.run {
                withContext(Dispatchers.Main) {
                    setAdapter()
                }
            }
        }
    }

    private fun insertMaterialDataInDb(data: List<RequestedMaterialResponseData>) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data.forEach {
                    appDataBaseClient?.requestedMaterialListDao()?.insert(it)
                }
            }.run {
                delay(400)
                withContext(Dispatchers.Main) {
                    "v3/listrequestedMaterial".updateTimeOnDb()
                    getDataFromDB()
                }
            }
        }
    }

    private fun setAdapter() {
        requestedMaterialAdapter =
            RequestMaterialAdapter(requestedDataList, this@MyDemoRequestActivity)
        rvRequestedMaterialRecycler.adapter = requestedMaterialAdapter
        requestedMaterialAdapter?.setClickListener(this)

        if (requestedDataList.size > 0) {
            rvRequestedMaterialRecycler.visibility = View.VISIBLE
            tvNoData.visibility = View.GONE
        } else {
            rvRequestedMaterialRecycler.visibility = View.GONE
            tvNoData.visibility = View.VISIBLE
        }
    }

    private fun setListeners() {
        imgAddDemo.setOnClickListener(this)
        imgFilter.setOnClickListener(this)
        swipeRefreshLayout.setOnRefreshListener(this)
    }

    override fun onRefresh() {
        swipeRefreshLayout.isRefreshing = true
        getDataForRequestedMaterial("1")
    }

    override fun onClickRequestedMaterial(data: RequestedMaterialResponseData) {
        val bundle = Bundle()
        bundle.putString("requested_data", Gson().toJson(data))
        launchIntent(RequestMaterialDetailActivity::class.java, bundle, false)
    }
}