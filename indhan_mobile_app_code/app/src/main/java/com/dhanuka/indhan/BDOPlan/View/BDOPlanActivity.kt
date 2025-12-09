package com.dhanuka.indhan.PlanATP.View

import android.os.Bundle
import android.os.Handler
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.DbStructure.MyContentObserver
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpList
import com.dhanuka.indhan.DataBase.TableStructureModels.BDOPlanS1Table
import com.dhanuka.indhan.DataSyncServices.DataSyncManager
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.BDOPlanListAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_syllabus_bdo_description.*

/**
 * Created by John on 5/15/2018.
 */
class BDOPlanActivity : BaseActivity(), BDOPlanListAdapter.onClickListener,
    SyncServiceInterfaceFactory.FailureListener, MyContentObserver.DbValueChangeListener,
    SwipeRefreshLayout.OnRefreshListener {
    override fun onFailure(msg: String) {
        hideProgress()
    }

    override fun onDBSyncSuccess() {
        hideProgress()
    }


    lateinit var preference: Preference
    lateinit var adapter: BDOPlanListAdapter
    lateinit var manager: DataSyncManager
    lateinit var observer: MyContentObserver
    var atpList: ArrayList<PlanAtpList> = ArrayList()


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_syllabus_bdo_description)
        btnlayout.visibility = View.GONE
        pd_name.text = "BDO PLan"
        iCross.setOnClickListener(this)
        preference = Preference(this)
        swipe_layout.setOnRefreshListener(this)
        atpList = DatabaseManager.getBDOPlanList(this)
        atpList.sort()
        adapter = BDOPlanListAdapter(atpList)
        adapter.setClickListener(this)
        rcViewProdDes.layoutManager =
            LinearLayoutManager(this)
        rcViewProdDes.adapter = adapter
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            manager = DataSyncManager(this, this)
            manager.getBDOPlanListDorS1()
            showProgress()
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
        observer = MyContentObserver()
        observer.setListener(this)
        applicationContext.contentResolver
            .registerContentObserver(BDOPlanS1Table.CONTENT_URI, true, observer)
    }


    override fun onRefresh() {
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            manager = DataSyncManager(this, this)
            manager.getBDOPlanListDorS1()
        }
        Handler().postDelayed(Runnable {
            swipe_layout.setRefreshing(false)
        }, 3000)

    }

    override fun onClick(p0: View?) {
        when (p0?.id) {
            R.id.iCross -> {
                finish()
            }
        }
    }

    override fun onSelected(model: PlanAtpList) {
        if (model.activites != 0) {
            val b = Bundle()
            b.putSerializable("bdo_name", model.name)
            b.putSerializable("bdo_id", model.id)
            b.putSerializable("month", model.month)
            launchIntent(BDOPlanDetailActivity::class.java, b, false)
        } else {
            makeToast("No activities are defined in this product")
        }
    }

    override fun onInvalidAttempt() {
        hideProgress()
    }

    override fun OnDbValueChange() {
        runOnUiThread(Runnable {
            atpList.clear()
            var list = DatabaseManager.getBDOPlanList(this)
            atpList.addAll(list)
            atpList.size
            adapter.notifyDataSetChanged()
            if (atpList.size > 0) {
                rcViewProdDes.visibility = View.VISIBLE
                tv_nodata.visibility = View.GONE
            } else {
                tv_nodata.text = "No BDO Plan Available"
                rcViewProdDes.visibility = View.GONE
                tv_nodata.visibility = View.VISIBLE
            }
        })
    }

}
