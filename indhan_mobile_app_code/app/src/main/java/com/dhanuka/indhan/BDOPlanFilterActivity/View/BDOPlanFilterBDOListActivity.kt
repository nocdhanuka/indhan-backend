package com.dhanuka.indhan.PlanATP.View

import android.os.Bundle
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse
import com.dhanuka.indhan.MySyllabusBDO.View.MySyllabusActivity
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.BDOAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.activity_bdo_plan_list.*

/**
 * Created by John on 5/15/2018.
 */
class BDOPlanFilterBDOListActivity : BaseActivity(), BDOAdapter.BDOListAdapterListener {
    lateinit var preference: Preference
    lateinit var adapter: BDOAdapter
    var atpList: ArrayList<BDOFilterResponse.BDOList> = ArrayList()
    var name_st = ""
    var menuType: String? = ""


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_bdo_plan_list)
        pd_name.text = "BDO PLan"
        var b: Bundle = intent.extras!!
        if (b != null) {
            var temp = b.getSerializable("model") as ArrayList<BDOFilterResponse.BDOList>
            name_st = b.getString("name")!!
            atpList.addAll(temp)
            menuType = b.getString("menu")
        }
        pd_name.text = name_st
        iCross.setOnClickListener(this)
        preference = Preference(this)
        adapter = BDOAdapter(atpList)
        adapter.setClickListener(this)
        rcViewProdDes.layoutManager =
            LinearLayoutManager(this)
        rcViewProdDes.adapter = adapter
    }

    override fun onBDOSelected(model: BDOFilterResponse.BDOList) {
        val b = Bundle()
        if (menuType.equals("bdo")) {
            b.putSerializable("bdo_name", model.bdoNAME)
            b.putSerializable("bdo_id", model.bdoID)
            launchIntent(BDOPlanDetailActivity::class.java, b, false)
        } else {
            b.putInt("bdo_id", model.bdoID)
            launchIntent(MySyllabusActivity::class.java, b, false)
        }
    }

    override fun onClick(p0: View?) {
        when (p0?.id) {
            R.id.iCross -> {
                finish()
            }
        }
    }


}
