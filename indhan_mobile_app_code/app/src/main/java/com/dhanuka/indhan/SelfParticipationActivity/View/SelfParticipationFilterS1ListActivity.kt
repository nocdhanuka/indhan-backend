package com.dhanuka.indhan.PlanATP.View

import android.os.Bundle
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.S1Adapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.activity_bdo_plan_list.*

/**
 * Created by John on 5/15/2018.
 */
class SelfParticipationFilterS1ListActivity : BaseActivity(), S1Adapter.AreaManagerAdapterListener {

    lateinit var preference: Preference
    lateinit var adapter: S1Adapter
    var atpList: ArrayList<BDOFilterResponse.AreaManagerList> = ArrayList()
    var name_st = ""
    var menuType: String? = ""


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_bdo_plan_list)
        pd_name.text = "BDO PLan"
        var b: Bundle = intent.extras!!
        pd_name.text = name_st
        iCross.setOnClickListener(this)
        preference = Preference(this)
        adapter = S1Adapter(atpList)
        adapter.setClickListener(this)
        rcViewProdDes.layoutManager =
            LinearLayoutManager(this)
        rcViewProdDes.adapter = adapter
    }

    override fun onAMSelected(model: BDOFilterResponse.AreaManagerList) {
        val b = Bundle()
        b.putSerializable("am_name", model.areaNAME)
        b.putSerializable("am_id", model.areaManagerID)
        launchIntent(BDOPlanDetailActivity::class.java, b, false)
    }


    override fun onClick(p0: View?) {
        when (p0?.id) {
            R.id.iCross -> {
                finish()
            }
        }
    }


}
