package com.dhanuka.indhan.institutional.activity.teaEstate

import android.os.Bundle
import android.text.TextUtils
import android.view.View
import com.bumptech.glide.Glide
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.activity.TestimonialsListActivity
import com.dhanuka.indhan.institutional.adapter.teaEstate.BranchListAdapter
import com.dhanuka.indhan.institutional.adapter.teaEstate.ViewContactAdapter
import com.dhanuka.indhan.institutional.model.teaEstate.ContactListData
import com.dhanuka.indhan.institutional.model.teaEstate.TeasEstateListData
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.Currency
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_view_institutional_detail.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext

class TeaEstateDetailActivity : BaseActivity(), ViewContactAdapter.OnClickItem,
    BranchListAdapter.OnClickViewBranchItem {

    private var preference: Preference? = null
    private var appDatabase: AppDatabase? = null
    private var arrayListOfData: ArrayList<ContactListData> = ArrayList()
    private var arrayListOfBranchData: ArrayList<TeasEstateListData> = ArrayList()
    private var addContactAdapter: ViewContactAdapter? = null
    private var branchListAdapter: BranchListAdapter? = null
    private var teaEstate: TeasEstateListData? = null
    private var parentId: String = ""
    private var teaEstateType: String = ""
    private var userType: String = ""

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.btnVisitHistory -> {
                val bundle = Bundle()
                bundle.putString("parentId", parentId)
                bundle.putString("type", teaEstateType)
                bundle.putString("for", "visit_history")
                launchIntent(TeaEstateAtpListActivity::class.java, bundle, true)
            }
            R.id.btnViewTestimonial -> {
                val bundle = Bundle()
                bundle.putString("parentId", parentId)
                bundle.putString("type", "1")
                launchIntent(TestimonialsListActivity::class.java, bundle, true)
            }
            R.id.btnAddMoreContact -> {
                val bundle = Bundle()
                bundle.putString("parentId", parentId)
                bundle.putString("type", teaEstateType)
                launchIntent(AddMoreContactActivity::class.java, bundle, true)
            }
            R.id.btnAddNewBranch -> {
                val bundle = Bundle()
                bundle.putString("parentId", parentId)
                bundle.putString("type", teaEstateType)
                bundle.putString("parentName", tvCompanyName.text.toString().trim())
                bundle.putString("branch", "yes")
                launchIntent(AddTeaEstateStgBranchActivity::class.java, bundle, true)
            }
            R.id.ivBack -> {
                finish()
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_view_institutional_detail)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        userType = preference?.getString("user_type") ?: ""
        intent?.getStringExtra("tea_data")?.let {
            teaEstate = Gson().fromJson(it, TeasEstateListData::class.java)
            parentId = (teaEstate?.tea_estate_id ?: "").toString()
            teaEstateType = (teaEstate?.tea_estate_type ?: "").toString()
            teaEstate?.contact_data?.let { contactList ->
                contactList.forEach() { contact ->
                    arrayListOfData.add(contact)
                }
            }

            GlobalScope.launch {
                withContext(Dispatchers.IO) {
                    arrayListOfBranchData = (appDatabase?.teaEstateListDao()
                        ?.allTeaEstatesBranchByOfficeTypeAndTeaEstateType(
                            teaEstate?.tea_estate_id ?: 0, "2",
                        ) ?: ArrayList<TeasEstateListData>()) as ArrayList<TeasEstateListData>
                }.run {
                    withContext(Dispatchers.Main) {
                        setAdapter()
                        setData()
                    }
                }
            }
        }

        intent?.getStringExtra("from_tea").let {
            if (it != null) {
                btnAddNewBranch.visibility = View.VISIBLE
                relBranchDetail.visibility = View.VISIBLE
            } else {
                relBranchDetail.visibility = View.GONE
                btnAddNewBranch.visibility = View.GONE
            }
        }

        btnVisitHistory.setOnClickListener(this)
        btnAddNewBranch.setOnClickListener(this)
        btnAddMoreContact.setOnClickListener(this)
        ivBack.setOnClickListener(this)
        btnViewTestimonial.setOnClickListener(this)
    }

    private fun setData() {
        Glide.with(this)
            .asBitmap()
            .load(Constants.IMAGEBASEURL + teaEstate?.image)
            .into(ivCompanyImage)

        officeType.text =
            if (teaEstate?.tea_estate_type == "1") "TeaEstate - ".plus(if (teaEstate?.office_type == "1") "Head Office" else "Branch Office") else "STG - ".plus(
                if (teaEstate?.office_type == "1") "Head Office" else "Branch Office"
            )
        tvManagerName.text = "Assigned To - ".plus(teaEstate?.fl_name)
        tvFlName.visibility = View.GONE
        tvCompanyName.text = teaEstate?.name
        tvCategoryData.text = teaEstate?.company_category
        if (teaEstate?.office_type == "1") {
            llType.visibility = View.VISIBLE
            llTurnOverWords.visibility = View.VISIBLE
            llDistrict.visibility = View.VISIBLE
        } else {
            llType.visibility = View.GONE
            llTurnOverWords.visibility = View.GONE
            llDistrict.visibility = View.GONE
        }
        tvTurnover.text = teaEstate?.turn_over
        if (!TextUtils.isEmpty(teaEstate?.turn_over)) {
            "In Words - ${
                Currency.convertToIndianCurrency(
                    teaEstate?.turn_over.toString().trim()
                )
            }".also { tvInWords.text = it }
        }
        tvLandArea.text = teaEstate?.land_area
        tvCompanyType.text = getCompanyType(teaEstate?.estate_company_id)
        tvAddress.text = teaEstate?.address
    }


    private fun getCompanyType(sugarmillCompanyId: Int?): CharSequence {
        return when (sugarmillCompanyId) {
            1 -> {
                "GOVT"
            }
            2 -> {
                "PUBLIC"
            }
            3 -> {
                "PRIVATE"
            }
            else -> {
                ""
            }
        }
    }

    private fun setAdapter() {
        //Contact Adapter
        addContactAdapter = ViewContactAdapter(this@TeaEstateDetailActivity, arrayListOfData)
        rcvContact.adapter = addContactAdapter
        addContactAdapter?.setClickListener(this)

        //Branch Adapter
        branchListAdapter =
            BranchListAdapter(this@TeaEstateDetailActivity, arrayListOfBranchData)
        rcvBranch.adapter = branchListAdapter
        branchListAdapter?.setViewClickListener(this)
    }

    override fun onClickView(view: View, subMenu: ContactListData) {
        //contact
    }

    override fun onClickView(view: View, subMenu: TeasEstateListData) {
        //branch
        val bundle = Bundle()
        bundle.putString("tea_data", Gson().toJson(subMenu))
        launchIntent(TeaEstateDetailActivity::class.java, bundle, true)
    }
}