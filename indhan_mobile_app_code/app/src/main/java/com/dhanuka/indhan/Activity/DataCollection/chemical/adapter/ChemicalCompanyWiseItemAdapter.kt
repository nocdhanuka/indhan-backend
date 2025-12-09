package com.dhanuka.indhan.Activity.DataCollection.chemical.adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Activity.DataCollection.chemical.ChemicalViewCompanyWiseActivity
import com.dhanuka.indhan.Model.CompanyModel
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.item_company_view_selection.view.*

class ChemicalCompanyWiseItemAdapter(
    itemList: ArrayList<CompanyModel>,
    categoryList: ArrayList<String>,
    chemicalAddCropSubActivity: ChemicalViewCompanyWiseActivity
) :
    RecyclerView.Adapter<ChemicalCompanyWiseItemAdapter.MyViewHolder>() {
    private var itemListData = ArrayList<CompanyModel>()
    private var categoryData = ArrayList<String>()
    var context: ChemicalViewCompanyWiseActivity
    var listener: AddNewItemListAdapter

    init {
        this.itemListData = itemList
        this.listener = chemicalAddCropSubActivity
        this.categoryData = categoryList
        this.context = chemicalAddCropSubActivity
    }

    override fun onCreateViewHolder(parent: ViewGroup, view: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_company_view_selection, parent, false)
        return MyViewHolder(itemView)
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var itemViews: View = view

        fun bindItems(
            companyModel: CompanyModel,
            position: Int,
            listener: AddNewItemListAdapter,
            context: ChemicalViewCompanyWiseActivity,
            categoryData: ArrayList<String>
        ) {
            itemViews.txtAddVolume.isFocusable = false
            itemViews.txtAddVolume.isFocusableInTouchMode = false
            itemViews.txtAddVolume.isClickable = false
            itemViews.txtAddMore.visibility = View.GONE
//            if (position == (itemListData.size - 1)) {
//                itemViews.txtAddMore.visibility = View.VISIBLE
//            }
            if (itemListData[position].categoryData != null) {
                itemViews.textCategory.text = itemListData[position].categoryData.name
            }
            if (itemListData[position].volumeData != null) {
                if (itemListData[position].volumeData != 0.0) {
                    itemViews.txtAddVolume.setText(itemListData[position].volumeData.toString()+ " %")
                }
            }

//            if (itemListData[position].edit) {
//                itemViews.txtAddVolume.isFocusable = true
//                itemViews.txtAddVolume.isFocusableInTouchMode = true
//                itemViews.txtAddVolume.isClickable = true
//                itemViews.textCategory.setCompoundDrawablesWithIntrinsicBounds(
//                    0,
//                    0,
//                    R.drawable.downarrow,
//                    0
//                )
//                itemViews.txtAddVolume.addTextChangedListener(object : TextWatcher {
//                    override fun beforeTextChanged(
//                        s: CharSequence?,
//                        start: Int,
//                        count: Int,
//                        after: Int
//                    ) {
//
//                    }
//
//                    override fun onTextChanged(
//                        s: CharSequence?,
//                        start: Int,
//                        before: Int,
//                        count: Int
//                    ) {
//                        if (s.toString().isNotEmpty()) {
//                            if (!TextUtils.isEmpty(itemViews.textCategory.text.toString())) {
//                                listener.onAllDataCollect(
//                                    itemViews.textCategory.text.toString(),
//                                    s.toString().trim(),
//                                    companyModel,
//                                    position
//                                )
//                            }
//                        }
//                    }
//
//                    override fun afterTextChanged(s: Editable?) {
//
//                    }
//                })
//            }
//            itemViews.ivDelete.setOnClickListener {
//                listener.onDeleteClick(position)
//            }
//            itemViews.ivEdit.setOnClickListener {
//                listener.onEditClick(position)
//            }
//            itemViews.txtAddMore.setOnClickListener {
//                itemViews.txtAddVolume.clearFocus()
//                context.hideSoftKeyboard()
//                if (!TextUtils.isEmpty(
//                        itemViews.textCategory.text
//                    ) && !TextUtils.isEmpty(itemViews.txtAddVolume.text.toString().trim())
//                ) {
//                    listener.onAllDataCollect(
//                        itemViews.textCategory.text.toString(),
//                        itemViews.txtAddVolume.text.toString().trim(),
//                        companyModel,
//                        position
//                    )
//                    listener.onAddClick(position)
//                } else {
//                    context.makeToast("Please fill row data first to add more.")
//                }
//            }
//            setCategoryDataDropDown(categoryData, itemViews, position, companyModel)
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
        holder: View,
        pos: Int,
        companyModel: CompanyModel
    ) {
        val selectCategory =
            NumberSpinner(context, categoryData, "Search or Select Category")
        holder.textCategory.setOnClickListener {
            if (itemListData[pos].edit) {
                if (!selectCategory.isEnable) {
                    selectCategory.showSpinerDialog()
                }
            }
        }
        selectCategory.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                holder.textCategory.text = item
                listener.onAllDataCollect(
                    holder.textCategory.text.toString(),
                    holder.txtAddVolume.text.toString().trim(),
                    companyModel,
                    pos
                )
            }
        })
    }

    override fun getItemCount(): Int = itemListData.size

    interface AddNewItemListAdapter {
        fun onAllDataCollect(category: String, volume: String, model: CompanyModel, position: Int)
        fun onDeleteClick(position: Int)
        fun onEditClick(position: Int)
        fun onAddClick(position: Int)
    }
}