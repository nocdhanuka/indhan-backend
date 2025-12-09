package com.dhanuka.indhan.SelfParticipationActivity.View

import android.os.Bundle
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.BDOPlanFilterActivity.MVP.BDOFilterFactory
import com.dhanuka.indhan.BDOPlanFilterActivity.MVP.BDOFilterPresenterImpl
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse
import com.dhanuka.indhan.PlanATP.View.SelfParticipationActivity
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.AreaManagerListAdapter
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.BDOListAdapter
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.RegionalManagerListAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_filter_bdo.*

/**
 * Created by John on 6/6/2018.
 */
class SelfParticipationFilterActivity : BaseActivity(), SwipeRefreshLayout.OnRefreshListener,
    RegionalManagerListAdapter.RegionalManagerListener,
    AreaManagerListAdapter.AreaManagerListListener, BDOFilterFactory.BDOFilterView,
    BDOListAdapter.BDOListAdapterListener {

    lateinit var regionalAdapter: RegionalManagerListAdapter
    lateinit var areaAdapter: AreaManagerListAdapter
    lateinit var bdoAdapter: BDOListAdapter
    lateinit var presenter: BDOFilterFactory.BDOFilterPresenter
    lateinit var token: String
    var userType: String? = ""

    //  var menuType: String? = ""
    lateinit var preference: Preference
    var listAreaManager: ArrayList<BDOFilterResponse.AreaManagerList> = ArrayList()
    var listRegionalManager: ArrayList<BDOFilterResponse.RegionalManagerList> = ArrayList()
    var listBDO: ArrayList<BDOFilterResponse.BDOList> = ArrayList()
    lateinit var model_major: BDOFilterResponse
    var bundle: Bundle? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_filter_bdo)
        swipe_layout.setOnRefreshListener(this)

        /*val bun=intent.extras
        if(bun!=null){
            menuType=bun.getString("menu")
        }*/

        preference = Preference(this)
        token = preference.getString("token")
        presenter = BDOFilterPresenterImpl(this)
        regionalAdapter = RegionalManagerListAdapter(listRegionalManager, this)
        regionalAdapter.setClickListener(this)
        iCross.setOnClickListener(this)
        submit.setOnClickListener(this)
        bdoAdapter = BDOListAdapter(listBDO)
        bdoAdapter.setClickListener(this)
        rcViewParent.layoutManager =
            LinearLayoutManager(this)
        rcviewChild.layoutManager =
            LinearLayoutManager(this)
        userType = preference.getString("user_type")
        pd_name.text = "Syllabus History"
        /*if(menuType.equals("bdo")){
        }else{
            pd_name.text="Syllabus History"
        }*/
        if (!userType.isNullOrEmpty()) {
            if (userType.equals("ZONAL", false)) {
                areaAdapter = AreaManagerListAdapter(listAreaManager, this, false)
                areaAdapter.setClickListener(this)
                rcviewChild.adapter = areaAdapter
                rcViewParent.adapter = regionalAdapter
            } else if (userType.equals("REGIONAL", false)) {
                areaAdapter = AreaManagerListAdapter(listAreaManager, this, true)
                areaAdapter.setClickListener(this)
                rcViewParent.adapter = areaAdapter
                rcviewChild.adapter = bdoAdapter
            }
        }
    }

    override fun onResume() {
        super.onResume()
        if (CheckConnectionStatus.isInternetAvailable(applicationContext)) {
            presenter.onRequest(preference.getString("token"))
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    override fun onRefresh() {
        swipe_layout.setRefreshing(false)
        if (CheckConnectionStatus.isInternetAvailable(applicationContext)) {
            presenter.onRequest(preference.getString("token"))
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.iCross -> {
                finish()
            }
            R.id.submit -> {
                if (userType.equals("ZONAL", false))
                    proceedtoNextScreen(true)
                else proceedtoNextScreen(false)
            }
        }
    }

    override fun onSuccess(model: BDOFilterResponse) {
        model_major = model
        if (userType.equals("ZONAL", false)) {
            parent_label.text = "S2 categories"
            child_label.text = "S1 categories"
            val tRegionalManager =
                model.data.regionalManagerList as ArrayList<BDOFilterResponse.RegionalManagerList>
            listRegionalManager.clear()
            listRegionalManager.addAll(tRegionalManager)
            val tAreaManager =
                model.data.areaManagerList as ArrayList<BDOFilterResponse.AreaManagerList>
            listAreaManager.clear()
            listAreaManager.addAll(tAreaManager)
            val tBDO = model.data.bdoList as ArrayList<BDOFilterResponse.BDOList>
            listBDO.clear()
            listBDO.addAll(tBDO)
            regionalAdapter.notifyDataSetChanged()
            if (listRegionalManager.size > 0) {
                onRegionalManagerSelected(listRegionalManager.get(0))
                nodata.visibility = View.GONE
                data_layer.visibility = View.VISIBLE
            } else {
                nodata.visibility = View.VISIBLE
                data_layer.visibility = View.GONE
            }
        } else if (userType.equals("REGIONAL", false)) {
            parent_label.text = "S1 categories"
            child_label.text = "BDO categories"
            val tAreaManager =
                model.data.areaManagerList as ArrayList<BDOFilterResponse.AreaManagerList>
            listAreaManager.clear()
            listAreaManager.addAll(tAreaManager)
            val tBDO = model.data.bdoList as ArrayList<BDOFilterResponse.BDOList>
            listBDO.clear()
            listBDO.addAll(tBDO)
            areaAdapter.notifyDataSetChanged()

            if (listBDO.size > 0) {
                onAreaManagerSelected(listAreaManager.get(0))
                nodata.visibility = View.GONE
                data_layer.visibility = View.VISIBLE
            } else {
                nodata.visibility = View.VISIBLE
                data_layer.visibility = View.GONE
            }

        }
    }

    override fun onFailure(msg: String) {
        makeToast(msg)
    }

    override fun onRegionalManagerSelected(model: BDOFilterResponse.RegionalManagerList) {
        val list_temp = ArrayList<BDOFilterResponse.AreaManagerList>()
        val regionalManagerList = ArrayList<BDOFilterResponse.RegionalManagerList>()
        if (model_major.data.areaManagerList.size > 0) {
            model_major.data.areaManagerList.forEach {
                it.isSelected = false
                if (it.regionalManagerID == model.regionalManagerID) {
                    list_temp.add(it)
                }
            }
        }
        bundle = null
        if (model_major.data.regionalManagerList.size > 0) {
            model_major.data.regionalManagerList.forEach {
                it.isSelected = false
                if (it.regionalManagerID == model.regionalManagerID)
                    it.isSelected = true
                regionalManagerList.add(it)
            }
        }
        listRegionalManager.clear()
        listRegionalManager.addAll(regionalManagerList)
        regionalAdapter.notifyDataSetChanged()



        listAreaManager.clear()
        listAreaManager.addAll(list_temp)
        areaAdapter.notifyDataSetChanged()
    }

    override fun onAreaManagerSelected(model: BDOFilterResponse.AreaManagerList) {
        /**
         * change view of area manager list.
         */
        val list_area = ArrayList<BDOFilterResponse.AreaManagerList>()
        model_major.data.areaManagerList.forEach {
            if (it.regionalManagerID == model.regionalManagerID) {
                it.isSelected = false
                if ((it.areaManagerID == model.areaManagerID))
                    it.isSelected = true
                list_area.add(it)
            }
        }
        listAreaManager.clear()
        listAreaManager.addAll(list_area)
        areaAdapter.notifyDataSetChanged()


        /**
         * filter bdo according to the area manager
         */
        if (userType.equals("ZONAL", false)) {
            val list_temp = ArrayList<BDOFilterResponse.BDOList>()
            model_major.data.bdoList.forEach {
                it.isSelected = false
                if (it.areaManagerID == model.areaManagerID) {
                    list_temp.add(it)
                }
            }
            bundle = null
            if (list_temp.size > 0) {
                bundle = Bundle()
                bundle!!.putSerializable("model", list_temp)
                bundle!!.putString("name", model.areaNAME)
                //     bundle!!.putString("menu", menuType)
            }


        } else if (userType.equals("REGIONAL", false)) {
            val list_temp = ArrayList<BDOFilterResponse.BDOList>()
            if (model_major.data.bdoList.size > 0) {
                model_major.data.bdoList.forEach {
                    if (it.areaManagerID == model.areaManagerID) {
                        list_temp.add(it)
                    }
                }
            }
            listBDO.clear()
            listBDO.addAll(list_temp)
            bdoAdapter.notifyDataSetChanged()
        }
    }

    override fun onBDOSelected(model: BDOFilterResponse.BDOList) {
        /* val list_area = ArrayList<BDOFilterResponse.BDOList>()
         model_major.data.bdoList.forEach {
             if (it.areaManagerID == model.areaManagerID) {
                 it.isSelected = false
                 if ((it.bdoID == model.bdoID))
                     it.isSelected = true
                 list_area.add(it)
             }
         }
         listBDO.clear()
         listBDO.addAll(list_area)
         bdoAdapter.notifyDataSetChanged()
         bundle = null
         if(menuType.equals("bdo")) {
             if (model != null) {
                 bundle = Bundle()
                 bundle!!.putSerializable("bdo_name", model.bdoNAME)
                 bundle!!.putSerializable("bdo_id", model.bdoID)
             }
         }else{
             if (model != null) {
                 bundle = Bundle()
                 bundle!!.putInt("bdo_id", model.bdoID)
             }
         }*/
    }

    fun proceedtoNextScreen(value: Boolean) {
        if (bundle != null) {
            if (value)
                launchIntent(SelfParticipationActivity::class.java, bundle, false)
        } else
            makeToast("No Data Present")
    }

}
