package com.dhanuka.indhan.Activity.DataCollection.chemical.adapter

import androidx.recyclerview.widget.RecyclerView
import android.text.Editable
import android.text.TextUtils
import android.text.TextWatcher
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Activity.DataCollection.chemical.ChemicalAddCompanySubActivity
import com.dhanuka.indhan.Model.CompanyModel
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.item_crop_selection.view.*

class CompanyItemListAdapter(
    itemList: ArrayList<CompanyModel>,
    categoryList: ArrayList<String>,
    chemicalAddCropSubActivity: ChemicalAddCompanySubActivity
) :
    RecyclerView.Adapter<CompanyItemListAdapter.MyViewHolder>() {
    private var itemListData = ArrayList<CompanyModel>()
    private var categoryData = ArrayList<String>()
    var context: ChemicalAddCompanySubActivity
    val listener: AddNewItemListAdapter

    init {
        this.itemListData = itemList
        this.listener = chemicalAddCropSubActivity
        this.categoryData = categoryList
        this.context = chemicalAddCropSubActivity
    }

    override fun onCreateViewHolder(parent: ViewGroup, view: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_company_selection, parent, false)
        return MyViewHolder(itemView)
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var itemViews: View = view

        fun bindItems(
            companyModel: CompanyModel,
            position: Int,
            listener: AddNewItemListAdapter,
            context: ChemicalAddCompanySubActivity,
            categoryData: ArrayList<String>
        ) {
            itemViews.txtAddMore.visibility = View.GONE
            if (position == (itemListData.size - 1)) {
                itemViews.txtAddMore.visibility = View.VISIBLE
            }
            itemViews.txtAddVolume.addTextChangedListener(object:TextWatcher{
                override fun beforeTextChanged(
                    s: CharSequence?,
                    start: Int,
                    count: Int,
                    after: Int
                ) {

                }

                override fun onTextChanged(s: CharSequence?, start: Int, before: Int, count: Int) {
                    if(!TextUtils.isEmpty(itemViews.textCategory.text.toString().trim())){
                        listener.onAllDataCollect(
                            itemViews.textCategory.text.toString(),
                            s.toString().trim(),
                            companyModel
                        )
                    }
                }

                override fun afterTextChanged(s: Editable?) {
                }

            })

            itemViews.txtAddMore.setOnClickListener {
                itemViews.txtAddVolume.clearFocus()
                context.hideSoftKeyboard()
                if (!TextUtils.isEmpty(
                        itemViews.textCategory.text
                    ) && !TextUtils.isEmpty(itemViews.txtAddVolume.text.toString().trim())
                ) {
                    listener.onAllDataCollect(
                        itemViews.textCategory.text.toString(),
                        itemViews.txtAddVolume.text.toString().trim(),
                        companyModel
                    )
                    listener.onAdd()
                } else {
                    context.makeToast("Please fill row data first to add more.")
                }
            }

            setCategoryDataDropDown(categoryData, itemViews)
        }
    }

    override fun getItemId(position: Int): Long {
        return itemListData[position].id
    }

    override fun setHasStableIds(hasStableIds: Boolean) {
        super.setHasStableIds(true)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        holder.bindItems(itemListData[position], position, listener, context, categoryData)
    }


    private fun setCategoryDataDropDown(
        categoryData: ArrayList<String>,
        holder: View
    ) {
        val selectCategory =
            NumberSpinner(context, categoryData, "Search or Select Category")
        holder.textCategory.setOnClickListener {
            if (!selectCategory.isEnable) {
                selectCategory.showSpinerDialog()
            }
        }
        selectCategory.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                holder.textCategory.text = item
            }
        })
    }

    override fun getItemCount(): Int = itemListData.size

    interface AddNewItemListAdapter {
        fun onAllDataCollect(category: String, volume: String, model: CompanyModel)
        fun onAdd()
    }
}