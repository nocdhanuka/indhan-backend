package com.dhanuka.indhan.institutional.activity.sugarMill

import android.content.Intent
import android.os.Bundle
import android.text.Editable
import android.text.TextUtils
import android.text.TextWatcher
import android.view.View
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.institutional.activity.teaEstate.TeaEstateFilterActivity
import com.dhanuka.indhan.institutional.adapter.sugarMill.SugarMillListAdapter
import com.dhanuka.indhan.institutional.model.RefreshEventModel
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillListData
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillListResponse
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_sugar_mill_list.*
import kotlinx.coroutines.*
import org.greenrobot.eventbus.EventBus
import org.greenrobot.eventbus.Subscribe
import org.greenrobot.eventbus.ThreadMode
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.text.SimpleDateFormat
import java.util.*
import kotlin.collections.set

class SugarMillListActivity : BaseActivity(), SugarMillListAdapter.OnClickItem,
    SwipeRefreshLayout.OnRefreshListener {

    private var preference: Preference? = null
    private var appDatabase: AppDatabase? = null
    private var sugarMillAdapter: SugarMillListAdapter? = null
    private var listOfData: ArrayList<SugarMillListData> = ArrayList()
    private var listOfCopyData: ArrayList<SugarMillListData> = ArrayList()
    private var userType: String = ""
    private var estate_type = 0
    private var state_id = ""
    private var state = ""
    private var district_id = ""
    private var district = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_sugar_mill_list)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        userType = preference?.getString("user_type") ?: ""
        searchBox.hint = "Search sugar mill..."
        setListeners()
        setAdapter()
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            getSugarMillsData(false)
        } else {
            getLocalData()
        }
    }

    private fun getLocalData() {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                listOfData = if (!userType.equals("bdo", ignoreCase = true)) {
                    (appDatabase?.sugarMillListDao()?.allSugarMillsCompany("1")
                        ?: ArrayList<SugarMillListData>()) as ArrayList<SugarMillListData>
                } else {
                    (appDatabase?.sugarMillListDao()?.allSugarMills()
                        ?: ArrayList<SugarMillListData>()) as ArrayList<SugarMillListData>
                }
                listOfCopyData = ArrayList(listOfData)
            }.run {
                withContext(Dispatchers.Main) {
                    setAdapter()
                }
            }
        }
    }

    private fun getSugarMillsData(b: Boolean) {
        swipeRefreshLayout.isRefreshing = false
        showProgress()
        val token = preference?.getString("token") ?: ""
        val query = HashMap<String, Any>()
        GlobalScope.launch {
            var timeData: ApiTimeTableModel? = null
            withContext(Dispatchers.IO) {
                timeData = "v3/getListSugarMill".getLastHitTimeData()
            }.run {
                withContext(Dispatchers.Main) {
                    val time = "${timeData?.updatedAt ?: 0}"
                    query["apiToken"] = token
                    query["updated_at"] = if (b) "0" else time
                    RetrofitClient.mvpClient
                        .create(ApiServiceRequest::class.java).getSugarMillListing(query)
                        .subscribeOn(Schedulers.newThread())
                        .observeOn(AndroidSchedulers.mainThread())
                        .subscribe(object : Subscriber<SugarMillListResponse>() {
                            override fun onCompleted() {
                                hideProgress()
                            }

                            override fun onError(e: Throwable?) {
                                hideProgress()
                            }

                            override fun onNext(it: SugarMillListResponse?) {
                                if (it != null) {
                                    if (it.status_code == 200) {
                                        insertDataInDb(it.data,b)
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

    private fun insertDataInDb(data: List<SugarMillListData>?, b: Boolean) {
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                if(b){
                    appDatabase?.sugarMillListDao()?.deleteTableData()
                }
                data?.let {
                    it.forEach() { sugarMill ->
                        appDatabase?.sugarMillListDao()?.insert(sugarMill)
                    }
                }
                "v3/getListSugarMill".updateTimeOnDb()
                delay(300)
            }.run {
                withContext(Dispatchers.Main) {
                    getLocalData()
                    hideProgress()
                }
            }
        }
    }

    private fun setAdapter() {
        listOfData.sortByDescending { Utility.getDateTimeStrFromJoda(it.created_at!!) }
        sugarMillAdapter = SugarMillListAdapter(this@SugarMillListActivity, listOfData)
        rcvSugarMillList.adapter = sugarMillAdapter
        sugarMillAdapter?.setClickListener(this)
        if (listOfData.size > 0) {
            tv_nodata.visibility = View.GONE
            rcvSugarMillList.visibility = View.VISIBLE
        } else {
            tv_nodata.visibility = View.VISIBLE
            rcvSugarMillList.visibility = View.GONE
        }
    }

    private fun setListeners() {
        ivAddNewSugarMill.setOnClickListener(this)
        swipeRefreshLayout.setOnRefreshListener(this)
        layout_btn.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        imgFilter.setOnClickListener(this)
        searchBox.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(s: CharSequence?, start: Int, count: Int, after: Int) {

            }

            override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {

            }

            override fun afterTextChanged(s: Editable?) {
                if (!TextUtils.isEmpty(s.toString())) {
                    listOfData.clear()
                    listOfCopyData.forEach() { estate ->
                        val name = estate.name ?: ""
                        if (name.lowercase().contains(s.toString().lowercase())) {
                            listOfData.add(estate)
                        }
                    }
                    GlobalScope.launch {
                        delay(300)
                        withContext(Dispatchers.Main) {
                            if (listOfData.size > 0) {
                                sugarMillAdapter?.notifyDataSetChanged()
                            } else {
                                makeToast("No matched data found.")
                                sugarMillAdapter?.notifyDataSetChanged()
                            }
                        }
                    }
                } else {
                    listOfData.clear()
                    listOfData.addAll(listOfCopyData)
                    sugarMillAdapter?.notifyDataSetChanged()
                }
            }

        })
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

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.ivAddNewSugarMill -> {
                launchIntent(AddSugarMillBranchActivity::class.java, false)
            }
            R.id.imgFilter -> {
                val intent = Intent(this@SugarMillListActivity, TeaEstateFilterActivity::class.java)
                intent.putExtra("from","Sugar Mill")
                intent.putExtra("estate_type", estate_type)
                intent.putExtra("state", state)
                intent.putExtra("state_id", state_id)
                intent.putExtra("district", district)
                intent.putExtra("district_id", district_id)
                startActivityForResult(intent, 2323)
            }
            R.id.ivBack -> {
                finish()
            }
            R.id.layout_btn -> {
                launchIntent(OfflineSugarMillData::class.java, false)
            }
            R.id.swipeRefreshLayout -> swipeRefreshLayout.post {
                swipeRefreshLayout.isRefreshing = true
            }
        }
    }

    override fun onClickView(view: View, subMenu: SugarMillListData) {
        if(subMenu.office_type == "1"){
            val bundle = Bundle()
            bundle.putString("sugarmill_data", Gson().toJson(subMenu))
            bundle.putString("from_tea", "1")
            launchIntent(SugarMillDetailActivity::class.java, bundle, false)
        }else{
            val bundle = Bundle()
            bundle.putString("sugarmill_data", Gson().toJson(subMenu))
            launchIntent(SugarMillDetailActivity::class.java, bundle, false)
        }
    }


    override fun onRefresh() {
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            state = ""
            state_id = ""
            district = ""
            district_id = ""
            estate_type = -1
            swipeRefreshLayout.isRefreshing = true
            getSugarMillsData(true)
        }
    }

    override fun onStart() {
        super.onStart()
        if (!EventBus.getDefault().isRegistered(this@SugarMillListActivity))
            EventBus.getDefault().register(this@SugarMillListActivity)
    }

    override fun onDestroy() {
        EventBus.getDefault().unregister(this@SugarMillListActivity)
        super.onDestroy()
    }

    @Subscribe(threadMode = ThreadMode.MAIN)
    public fun onMessageEvent(event: RefreshEventModel?) {
        if (event != null) {
            if (event.data == 1) {
                if (CheckConnectionStatus.isInternetAvailable(this@SugarMillListActivity)) {
                    getSugarMillsData(true)
                }
            }
        }
    }


    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (resultCode == RESULT_OK) {
            if (requestCode == 2323) {
                data?.getStringExtra("state")?.let {
                    state = it
                }

                data?.getStringExtra("state_id")?.let {
                    state_id = it
                }

                data?.getStringExtra("district_id")?.let {
                    district_id = it
                }

                data?.getStringExtra("district")?.let {
                    district = it
                }

                data?.getIntExtra("estate_type", -1)?.let {
                    estate_type = it
                }
                if (!TextUtils.isEmpty(state) || !TextUtils.isEmpty(district) || estate_type != -1) {
                    applyFilterData()
                } else {
                    listOfData.clear()
                    listOfData.addAll(listOfCopyData)
                    sugarMillAdapter?.notifyDataSetChanged()
                }
            }
        }
    }

    private fun applyFilterData() {
        listOfData.clear()
        listOfCopyData.forEach() {
            if (!TextUtils.isEmpty(state) && !TextUtils.isEmpty(district) && estate_type != -1) {
                if (it.state.equals(state, ignoreCase = true) && it.district.equals(
                        district,
                        ignoreCase = true
                    ) && it.sugarmill_type.equals("$estate_type", ignoreCase = true)
                ) {
                    listOfData.add(it)
                }
            } else if (!TextUtils.isEmpty(state) && !TextUtils.isEmpty(district)) {
                if (it.state.equals(state, ignoreCase = true) && it.district.equals(
                        district, ignoreCase = true
                    )
                ) {
                    listOfData.add(it)
                }
            } else if (!TextUtils.isEmpty(state) && estate_type != -1) {
                if (it.state.equals(state, ignoreCase = true)
                    && it.sugarmill_type.equals("$estate_type", ignoreCase = true)
                ) {
                    listOfData.add(it)
                }
            } else if (!TextUtils.isEmpty(district) && estate_type != -1) {
                if (it.district.equals(district, ignoreCase = true)
                    && it.sugarmill_type.equals("$estate_type", ignoreCase = true)
                ) {
                    listOfData.add(it)
                }
            } else if (!TextUtils.isEmpty(state)) {
                if (it.state.equals(state, ignoreCase = true)) {
                    listOfData.add(it)
                }
            } else if (!TextUtils.isEmpty(district)) {
                if (it.district.equals(district, ignoreCase = true)) {
                    listOfData.add(it)
                }
            } else if (estate_type != -1) {
                if (it.sugarmill_type.equals("$estate_type", ignoreCase = true)) {
                    listOfData.add(it)
                }
            }
            listOfData.distinctBy { it1 -> it1.sugarmill_type }
        }

        GlobalScope.launch {
            delay(300)
            withContext(Dispatchers.Main) {
                if (listOfData.size > 0) {
                    sugarMillAdapter?.notifyDataSetChanged()
                } else {
                    makeToast("No matched data found.")
                    sugarMillAdapter?.notifyDataSetChanged()
                }
            }
        }

    }

}