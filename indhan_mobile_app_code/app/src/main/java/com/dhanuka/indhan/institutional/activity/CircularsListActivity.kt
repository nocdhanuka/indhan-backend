package com.dhanuka.indhan.institutional.activity

import android.content.Intent
import android.net.Uri
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import androidx.activity.result.ActivityResult
import androidx.activity.result.ActivityResultCallback
import androidx.activity.result.contract.ActivityResultContracts
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.institutional.adapter.CircularListAdapter
import com.dhanuka.indhan.institutional.model.CircularListData
import com.dhanuka.indhan.institutional.model.CircularListResponse
import com.dhanuka.indhan.institutional.model.RefreshEventModel
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_circular_list.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import org.greenrobot.eventbus.Subscribe
import org.greenrobot.eventbus.ThreadMode
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*

class CircularsListActivity : BaseActivity(), SwipeRefreshLayout.OnRefreshListener,
    CircularListAdapter.OnClickItem {
    private var fromDateCreatedString: String = ""
    private var toDateCreatedString: String = ""
    private var fromDatePublishedString: String = ""
    private var toDatePublishedString: String = ""
    private var appDatabase: AppDatabase? = null
    private var preference: Preference? = null
    private var listOfData = ArrayList<CircularListData>()
    private var parentOfData = ArrayList<CircularListData>()
    private var circularAdapter: CircularListAdapter? = null

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.filterCircular -> {
                val intent = Intent(this@CircularsListActivity, FilterCircularsActivity::class.java)
                val b = Bundle()
                b.putString("created_from", fromDateCreatedString)
                b.putString("created_to", toDateCreatedString)
                b.putString("published_from", fromDatePublishedString)
                b.putString("published_to", toDatePublishedString)
                intent.putExtra("bundle", b)
                someActivityResultLauncher.launch(intent)
            }
            R.id.addCirculars -> {
                val intent = Intent(this@CircularsListActivity, AddCircularsActivity::class.java)
                someAddActivityResultLauncher.launch(intent)
            }
            R.id.swipeRefreshLayout -> swipeRefreshLayout.post {
                swipeRefreshLayout.isRefreshing = true
            }
            R.id.ivBack -> {
                finish()
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

    var someAddActivityResultLauncher = registerForActivityResult(
        ActivityResultContracts.StartActivityForResult(),
        ActivityResultCallback { result: ActivityResult ->
            if (result.resultCode == RESULT_OK) {
                getCircularsData(false)
            }
        } as ActivityResultCallback<ActivityResult>)

    private fun applyFilterToActivity(data: Intent?) {
        data?.getBundleExtra("bundle")?.let { it ->
            fromDateCreatedString = it.getString("created_from") ?: ""
            toDateCreatedString = it.getString("created_to") ?: ""
            fromDatePublishedString = it.getString("published_from") ?: ""
            toDatePublishedString = it.getString("published_to") ?: ""
            applyFilterToList()
        }
    }

    private fun applyFilterToList() {
        if (!TextUtils.isEmpty(fromDateCreatedString)
            || !TextUtils.isEmpty(toDateCreatedString)
            || !TextUtils.isEmpty(fromDatePublishedString)
            || !TextUtils.isEmpty(toDatePublishedString)
        ) {
            listOfData.clear()
            parentOfData.forEach() {
                if (Utility.isDateInBetweenIncludingEndPoints(
                        fromDateCreatedString,
                        toDateCreatedString,
                        it.created_at ?: ""
                    )
                    || Utility.isDateInBetweenIncludingEndPoints(
                        fromDatePublishedString,
                        toDatePublishedString,
                        it.published_date ?: ""
                    )
                ) {
                    listOfData.add(it)
                }
            }
            GlobalScope.launch {
                delay(300)
                withContext(Dispatchers.Main) {
                    if (listOfData.size > 0) {

                    } else {
                        makeToast("No matched data found.")
                    }
                    setAdapter()
                }
            }
        } else {
            listOfData.clear()
            listOfData.addAll(parentOfData)
            setAdapter()
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_circular_list)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        setListeners()
        val userType = preference?.getString("user_type") ?: ""
        if (!userType.contains("bdo", ignoreCase = true)) {
            addCirculars.visibility = View.VISIBLE
        } else {
            addCirculars.visibility = View.GONE
        }
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getCircularsData(false)
        } else {
            getLocalData()
        }
    }

    private fun getLocalData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                listOfData = (appDatabase?.circularsListDao()?.allCircularsList()
                    ?: ArrayList<CircularListData>()) as ArrayList<CircularListData>
                parentOfData = ArrayList<CircularListData>(listOfData)
            }.run {
                withContext(Dispatchers.Main) {
                    setAdapter()
                }
            }
        }
    }

    private fun setAdapter() {
        listOfData.sortByDescending { Utility.getDateTimeStrFromJoda(it.updated_at!!) }
        circularAdapter = CircularListAdapter(this@CircularsListActivity, listOfData)
        rvCircularList.adapter = circularAdapter
        circularAdapter?.setClickListener(this)
        if (listOfData.size > 0) {
            tvNoData.visibility = View.GONE
            rvCircularList.visibility = View.VISIBLE
        } else {
            tvNoData.visibility = View.VISIBLE
            rvCircularList.visibility = View.GONE
        }
    }

    private fun getCircularsData(b: Boolean) {
        swipeRefreshLayout.isRefreshing = false
        showProgress()
        val token = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = "v3/getCircular".getLastHitTimeData()
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_at"] = if (b) "0" else time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).getCirculars(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<CircularListResponse>() {
                            override fun onCompleted() {
                                hideProgress()
                            }

                            override fun onError(e: Throwable?) {
                                hideProgress()
                            }

                            override fun onNext(it: CircularListResponse?) {
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

    private fun insertDataInDb(data: List<CircularListData>?) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                data?.let {
                    it.forEach() { teaEstate ->
                        appDatabase?.circularsListDao()?.insert(teaEstate)
                    }
                }
                "v3/getCircular".updateTimeOnDb()
                delay(300)
            }.run {
                withContext(Dispatchers.Main) {
                    getLocalData()
                    hideProgress()
                }
            }
        }
    }

    private fun setListeners() {
        addCirculars.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        filterCircular.setOnClickListener(this)
        swipeRefreshLayout.setOnRefreshListener(this)
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

    private fun String.getLastHitTimeData(): ApiTimeTableModel? {
        return appDatabase?.apiTimeDao()?.getApiTimeData(this)
    }

    override fun onRefresh() {
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            swipeRefreshLayout.isRefreshing = true
            getCircularsData(true)
        }
    }

    override fun onClickView(view: View, subMenu: CircularListData, status: Int) {

//        if (CheckConnectionStatus.isInternetAvailable(this)) {
//            if (!TextUtils.isEmpty(subMenu.circular_path)) {
//                val intent = Intent(this@CircularsListActivity, WebViewAnyDataActivity::class.java)
//                intent.putExtra(
//                    "key",
//                    "${subMenu.circular_path}"
//                )
//                intent.putExtra(
//                    "title",
//                    "View Circular"
//                )
//                startActivity(intent)
//            }
//        } else {
//            makeToast(resources.getString(R.string.no_internet_connection))
//        }

        var url: Uri? = null
        url = if (status == 1) {
            Uri.parse(subMenu.circular_path.plus("/").plus(preference?.getString("ecn")))
        } else {
            Uri.parse(subMenu.circular_path)
        }
        val browserIntent = Intent(Intent.ACTION_VIEW, url)
        startActivity(browserIntent)
    }

    override fun onClickEdit(view: View, subMenu: CircularListData) {
        val bundle = Bundle()
        bundle.putString("circular_data", Gson().toJson(subMenu))
        val intent = Intent(this@CircularsListActivity, AddCircularsActivity::class.java)
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
        intent.putExtras(bundle)
        someAddActivityResultLauncher.launch(intent)
    }

    override fun onStart() {
        super.onStart()
        if (!EventBus.getDefault().isRegistered(this@CircularsListActivity))
            EventBus.getDefault().register(this@CircularsListActivity)
    }

    override fun onDestroy() {
        EventBus.getDefault().unregister(this@CircularsListActivity)
        super.onDestroy()
    }

    @Subscribe(threadMode = ThreadMode.MAIN)
    public fun onMessageEvent(event: RefreshEventModel?) {
        if (event != null) {
            if (event.data == 3) {
                if (CheckConnectionStatus.isInternetAvailable(this)) {
                    getCircularsData(true)
                }
            }
        }
    }

}