package com.dhanuka.indhan.Activity.DataCollection.chemical.adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Activity.DataCollection.chemical.ChemicalViewCropWiseActivity
import com.dhanuka.indhan.Model.CropModel
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.item_crop_wise_selection.view.*

class ChemicalCropWiseItemAdapter(
    itemList: ArrayList<CropModel>,
    categoryList: ArrayList<String>,
    chemicalAddCropSubActivity: ChemicalViewCropWiseActivity
) :
    RecyclerView.Adapter<ChemicalCropWiseItemAdapter.MyViewHolder>() {
    private var itemListData = ArrayList<CropModel>()
    private var categoryData = ArrayList<String>()
    private var subCategoryData = ArrayList<String>()
    var context: ChemicalViewCropWiseActivity
    var listener: AddNewItemListAdapter

    init {
        this.itemListData = itemList
        this.listener = chemicalAddCropSubActivity
        this.categoryData = categoryList
        this.context = chemicalAddCropSubActivity
    }

    override fun onCreateViewHolder(parent: ViewGroup, view: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_crop_wise_selection, parent, false)
        return MyViewHolder(itemView)
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var itemViews: View = view

        fun bindItems(
            companyModel: CropModel,
            position: Int,
            listener: AddNewItemListAdapter,
            context: ChemicalViewCropWiseActivity,
            categoryData: ArrayList<String>,
            subCategoryData: ArrayList<String>
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
            if (itemListData[position].subcategoryData != null) {
                itemViews.textSubCategory.text = itemListData[position].subcategoryData
            }
            if (itemListData[position].volumeData != null) {
                if (itemListData[position].volumeData != 0.0) {
                    itemViews.txtAddVolume.text = itemListData[position].volumeData.toString()+" %"
                }
            }

//            if (itemListData[position].isEdit) {
//                itemViews.txtAddVolume.isFocusable = true
//                itemViews.txtAddVolume.isFocusableInTouchMode = true
//                itemViews.txtAddVolume.isClickable = true
//                itemViews.textCategory.setCompoundDrawablesWithIntrinsicBounds(
//                    0,
//                    0,
//                    R.drawable.downarrow,
//                    0
//                )
//                itemViews.textSubCategory.setCompoundDrawablesWithIntrinsicBounds(
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
//                                    itemViews.textSubCategory.text.toString(),
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
//                        itemViews.textSubCategory.text.toString(),
//                        itemViews.txtAddVolume.text.toString().trim(),
//                        companyModel,
//                        position
//                    )
//                    listener.onAddClick(position)
//                } else {
//                    context.makeToast("Please fill row data first to add more.")
//                }
//            }
//            if(itemListData[position].isEdit) {
//                setCategoryDataDropDown(categoryData, itemViews, position)
//                setSubCategoryDataDropDown(subCategoryData, itemViews, position)
//            }
        }
    }

    private fun setSubCategoryDataDropDown(
        cropListData: java.util.ArrayList<String>,
        itemViews: View,
        position: Int
    ) {
        val selectClass =
            NumberSpinner(context, cropListData, "Search or Select Crop")
        itemViews.textSubCategory.setOnClickListener {
            if (!selectClass.isEnable) {
                selectClass.showSpinerDialog()
            }
        }
        selectClass.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                itemViews.textSubCategory.text = item
                itemViews.txtAddVolume.setText("")
            }
        })
    }

    override fun getItemId(position: Int): Long {
        return itemListData[position].id
    }

    override fun setHasStableIds(hasStableIds: Boolean) {
        super.setHasStableIds(true)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        holder.bindItems(itemListData[position], position, listener, context, categoryData,subCategoryData)
    }

    private fun setCategoryDataDropDown(
        categoryData: ArrayList<String>,
        holder: View,
        pos: Int
    ) {
        val selectCategory =
            NumberSpinner(context, categoryData, "Search or Select Category")
        holder.textCategory.setOnClickListener {
            if (itemListData[pos].isEdit) {
                if (!selectCategory.isEnable) {
                    selectCategory.showSpinerDialog()
                }
            }
        }
        selectCategory.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                holder.textCategory.text = item
                listener.onCategorySelected(item,pos)
            }
        })
    }

    override fun getItemCount(): Int = itemListData.size

    fun setCropData(subCategories: java.util.ArrayList<String>) {
        subCategoryData.clear()
        subCategoryData.addAll(subCategories)
    }

    interface AddNewItemListAdapter {
        fun onAllDataCollect(category: String,subcategory: String, volume: String, model: CropModel, position: Int)
        fun onCategorySelected(category: String, position: Int)
        fun onDeleteClick(position: Int)
        fun onEditClick(position: Int)
        fun onAddClick(position: Int)
    }
}