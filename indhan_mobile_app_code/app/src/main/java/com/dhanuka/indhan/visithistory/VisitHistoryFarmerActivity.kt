package com.dhanuka.indhan.visithistory

import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.validatefarmers.SuccessBean
import kotlinx.android.synthetic.main.activity_visit_history_farmer.*

class VisitHistoryFarmerActivity : BaseActivity(),
    VisitHistoryFarmerContractor.VisitHistoryFarmerView {
    override fun setCall(items: SuccessBean) {

    }

    override fun setChangeStatus(items: SuccessBean) {

    }

    override fun onClick(v: View?) {

    }

    override fun setItems(items: VisitHistoryFarmerBean) {
        if (items.data.size > 0) {
            setView(items)
        } else {
            txtNoItem.visibility = View.VISIBLE
        }
    }

    override fun failure(items: String) {
        makeToast(items)

    }


    private lateinit var mPresenter: VisitHistoryFarmerContractor.VisitHistoryFarmerPresenter
    private lateinit var preference: Preference
    private lateinit var adapter: VisitHistoryListAdapter
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_visit_history_farmer)

        preference = Preference(this)
        mPresenter = VisitHistoryFarmerPresenter(this)

        val query = HashMap<String, Any>()
        query["farmerId"] = intent?.getStringExtra("key") ?: ""
        query["apiToken"] = preference.getString("token")
        query["updated_at"] = "0"
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            mPresenter.onGetVistList(query, true)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }


    private fun setView(item: VisitHistoryFarmerBean) {
        adapter = VisitHistoryListAdapter(this, item.data)
        recyclerViewVisitHistory.layoutManager =
            LinearLayoutManager(this)
        recyclerViewVisitHistory.adapter = adapter

    }
}
