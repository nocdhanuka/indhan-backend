package com.dhanuka.indhan.PlanATP.View

import android.os.Bundle
import android.os.Handler
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.DbStructure.MyContentObserver
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpDetail
import com.dhanuka.indhan.DataSyncServices.DataSyncManager
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.BDOPlanDetailAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_syllabus_bdo_description.*

/**
 * Created by John on 5/15/2018.
 */
class BDOPlanDetailActivity : BaseActivity(),
    SyncServiceInterfaceFactory.FailureListener, /*MyContentObserver.DbValueChangeListener,*/
    SwipeRefreshLayout.OnRefreshListener {

    lateinit var preference: Preference
    lateinit var adapter: BDOPlanDetailAdapter
    lateinit var manager: DataSyncManager
    lateinit var observer: MyContentObserver
    var atpList: ArrayList<PlanAtpDetail> = ArrayList()
    var bdoName = ""
    var bdoid = 0
    var month = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_syllabus_bdo_description)
        btnlayout.visibility = View.GONE
        var b: Bundle = intent.extras!!
        if (b != null) {
            bdoName = b.getString("bdo_name")!!
            bdoid = b.getInt("bdo_id")
            month = b.getInt("month")
        }
        swipe_layout.setOnRefreshListener(this)
        pd_name.text = bdoName
        iCross.setOnClickListener(this)
        preference = Preference(this)
        atpList = DatabaseManager.getBDOPlanListDetail(this, bdoid)
        adapter = BDOPlanDetailAdapter(atpList)
        rcViewProdDes.layoutManager =
            LinearLayoutManager(this)
        rcViewProdDes.adapter = adapter
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            manager = DataSyncManager(this, this)
            manager.getBDOPlanListDetailS1(bdoid)
            showProgress()
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
        /*  observer = MyContentObserver()
          observer.setListener(this)
          getApplicationContext().getContentResolver().registerContentObserver(BDOPlanS1DetailTable.CONTENT_URI, true, observer)
    */
    }

    override fun onClick(p0: View?) {
        when (p0?.id) {
            R.id.iCross -> {
                finish()
            }
        }
    }

    override fun onInvalidAttempt() {
    }

    fun OnDbValueChange() {
        if (bdoid != 0) {
            atpList.clear()
            var list2 = ArrayList<PlanAtpDetail>()
            var list = DatabaseManager.getBDOPlanListDetail(this, bdoid)
            list.forEach {
                try {
                    if (it.planDate.contains("T")) {
                        it.planDate = Utility.getDateStrFromJoda(it.planDate)
                    }
                    it.date_obj = Utility.getDateObj(it.planDate)
                } catch (e: Exception) {
                    var tst = it.planDate
                    /* if(it.planDate.length>31)
                         it.date_obj = Utility.getDateObj(it.planDate.substring(0,10))*/
                }
                if (Utility.getMontfromDateObj(it.date_obj) == month) {
                    list2.add(it)
                }
            }

            if (month == 0) {
                atpList.addAll(list)
            } else {
                atpList.addAll(list2)
            }
            atpList.sort()
            updateui()
        }
    }

    fun updateui() {
        runOnUiThread(Runnable {
            adapter.notifyDataSetChanged()
            if (atpList.size > 0) {
                tv_nodata.visibility = View.GONE
                rcViewProdDes.visibility = View.VISIBLE
            } else {
                tv_nodata.visibility = View.VISIBLE
                rcViewProdDes.visibility = View.GONE
            }
        })
    }

    override fun onRefresh() {
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            manager = DataSyncManager(this, this)
            //     manager.getBDOPlanListDetailS1(bdoid)
        }
        Handler().postDelayed(Runnable {
            swipe_layout.setRefreshing(false)
        }, 3000)
    }

    override fun onDBSyncSuccess() {
        hideProgress()
        OnDbValueChange()
    }

    override fun onFailure(msg: String) {
        hideProgress()
        makeToast(msg)
        tv_nodata.visibility = View.VISIBLE
        rcViewProdDes.visibility = View.GONE
    }

}
