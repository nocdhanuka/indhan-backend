package com.dhanuka.indhan.SelfParticipationActivity.View

import android.content.Intent
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.AreaManagerSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.RegionalSpinnerDialog
import com.dhanuka.indhan.BDOPlanFilterActivity.MVP.BDOFilterFactory
import com.dhanuka.indhan.BDOPlanFilterActivity.MVP.BDOFilterPresenterImpl
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.SelectDateFragment
import kotlinx.android.synthetic.main.activity_sp_filter.*

/**
 * Created by John on 5/27/2018.
 */
class SPFilterActivity : BaseActivity(), BDOFilterFactory.BDOFilterView {

    lateinit var presenter: BDOFilterFactory.BDOFilterPresenter
    lateinit var preference: Preference
    var userType: String = ""
    lateinit var regionalDialog: RegionalSpinnerDialog
    var regionalList: ArrayList<BDOFilterResponse.RegionalManagerList> = ArrayList()
    lateinit var areaDialog: AreaManagerSpinnerDialog
    var areaList: ArrayList<BDOFilterResponse.AreaManagerList> = ArrayList()
    var areaAllList: ArrayList<BDOFilterResponse.AreaManagerList> = ArrayList()


    var manager_id = 0
    var manager_name = ""
    var manager_type = ""
    var sr_manager_id = 0
    var sr_manager_name = ""


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_sp_filter)
        presenter = BDOFilterPresenterImpl(this)
        // presenter_productlist = ProductListPresenterImpl(this)
        preference = Preference(this)
        iCross.setOnClickListener(this)
        reset.setOnClickListener(this)
        apply.setOnClickListener(this)
        s1.setOnClickListener(this)
        s2.setOnClickListener(this)
        userType = preference.getString("user_type")
        initFilter()
        initiateDialog()


    }

    private fun initFilter() {
        intent.getBundleExtra("bundle")?.let {
            sr_manager_id = it.getInt("sr_manager_id", 0)
            sr_manager_name = it.getString("sr_manager_name") ?: ""
            manager_id = it.getInt("product", 0)
            manager_type = it.getString("manager_level") ?: ""
            manager_name = it.getString("manager_name") ?: ""
        }
        if (manager_type.isNotEmpty()) {
            if (manager_type == "AREA") {
                if (sr_manager_id != 0) {
                    layout_s2.visibility = View.VISIBLE
                    s2.text = sr_manager_name
                }
                layout_s1.visibility = View.VISIBLE
                s1.text = manager_name
            } else if (manager_type.contains("REGIONAL")) {
                layout_s2.visibility = View.VISIBLE
                s2.text = manager_name
            }
        }

    }

    private fun initiateDialog() {
        regionalDialog = RegionalSpinnerDialog(this, regionalList, "Select Regional Manager")
        regionalDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                //  spinnerCrop.text = item
                s2.text = item
                manager_id = regionalList.get(position).regionalManagerID
                manager_name = regionalList.get(position).regionalNAME
                manager_type = "REGIONAL"
                var id = regionalList.get(position).regionalManagerID
                areaList.clear()
                if (areaAllList.size > 0) {
                    areaAllList.forEach {
                        if (it.regionalManagerID == id) {
                            areaList.add(it)
                        }
                    }
                    if (areaList.size > 0) {
                        areaDialog.updatelist(areaList)
                        layout_s1.visibility = View.VISIBLE
                    } else {
                        layout_s1.visibility = View.GONE
                    }
                } else {
                    layout_s1.visibility = View.GONE
                }
            }
        })
        areaDialog = AreaManagerSpinnerDialog(this, areaList, "Select Regional Manager")
        areaDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                s1.text = item
                if (manager_id != 0) {
                    sr_manager_id = manager_id
                    sr_manager_name = manager_name
                }
                manager_id = areaList.get(position).areaManagerID
                manager_name = areaList.get(position).areaNAME
                manager_type = "AREA"
            }
        })

    }

    override fun onResume() {
        super.onResume()

        if (userType.equals("ZONAL") || userType.contains("REGIONAL") || userType.contains("AREA")) {
            if (CheckConnectionStatus.isInternetAvailable(applicationContext)) {
                presenter.onRequest(preference.getString("token"))
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
        }
    }

    override fun onClick(view: View) {
        when (view.id) {


            R.id.iCross -> {
                finish()
            }
            R.id.s2 -> {
                if (!regionalDialog.isEnable)
                    regionalDialog.showSpinerDialog()
            }
            R.id.s1 -> {
                if (!areaDialog.isEnable)
                    areaDialog.showSpinerDialog()
            }
            R.id.apply -> {
                proceedApply()
            }
            R.id.reset -> {
                resetEverything()
            }
        }
    }

    private fun resetEverything() {

        manager_id = 0
        manager_name = ""
        manager_type = ""
        sr_manager_id = 0
        sr_manager_name = ""
        if (userType.equals("ZONAL")) {
            layout_s2.visibility = View.VISIBLE
            s2.text = "Select S2"
            layout_s1.visibility = View.GONE
            s1.text = "Select S1"
        }
        if (userType.equals("REGIONAL")) {
            layout_s1.visibility = View.VISIBLE
            s1.text = "Select S1"
        }

    }

    private fun proceedApply() {
        val intent = Intent()
        val b = Bundle()
        b.putInt("manager_id", manager_id)
        b.putString("manager_level", manager_type)
        b.putString("manager_name", manager_name)
        b.putInt("sr_manager_id", sr_manager_id)
        b.putString("sr_manager_name", sr_manager_name)

        intent.putExtra("bundle", b)
        setResult(101, intent)
        finish()
    }

    private fun launchDateicker(i: Int) {
        val bundle = Bundle()
        bundle.putInt("key", i)
        val newFragment = SelectDateFragment()
        newFragment.setArguments(bundle)
        newFragment.show(this@SPFilterActivity.supportFragmentManager, "DatePicker")
    }

    /**
     * list response
     */
    override fun onSuccess(model: BDOFilterResponse) {
        if (userType.equals("ZONAL")) {
            layout_s2.visibility = View.VISIBLE

            regionalList.clear()

            if (model.data.regionalManagerList != null) {
                regionalList.clear()
                if (model.data.regionalManagerList.size > 0) {
                    regionalList.addAll(model.data.regionalManagerList)
                }
                regionalDialog.updatelist(regionalList)
            }
            if (model.data.areaManagerList != null) {
                areaAllList.clear()
                if (model.data.areaManagerList.size > 0) {
                    areaAllList.addAll(model.data.areaManagerList)
                }
            }

        } else if (userType.contains("REGIONAL")) {
            layout_s1.visibility = View.VISIBLE

            if (model.data.areaManagerList != null) {
                areaAllList.clear()
                if (model.data.areaManagerList.size > 0) {
                    areaList.addAll(model.data.areaManagerList)
                }
                areaDialog.updatelist(areaList)
            }

        }

    }


    override fun onFailure(msg: String) {

    }

}
