package com.dhanuka.indhan.Activity.DataCollection.chemical.adapter

import androidx.recyclerview.widget.RecyclerView
import android.text.Editable
import android.text.TextUtils
import android.text.TextWatcher
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.Activity.DataCollection.chemical.ChemicalAddCropSubActivity
import com.dhanuka.indhan.Model.CropModel
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.item_crop_selection.view.*

class CropItemListAdapter(
    itemList: ArrayList<CropModel>,
    categoryList: ArrayList<String>,
    chemicalAddCropSubActivity: ChemicalAddCropSubActivity
) :
    RecyclerView.Adapter<CropItemListAdapter.MyViewHolder>() {
    private var itemListData = ArrayList<CropModel>()
    private var categoryData = ArrayList<String>()
    var cropListData = ArrayList<String>()
    var context: ChemicalAddCropSubActivity
    val listener: AddNewItemListAdapter

    init {
        this.itemListData = itemList
        this.listener = chemicalAddCropSubActivity
        this.categoryData = categoryList
        this.context = chemicalAddCropSubActivity
    }

    fun setCropData(list: ArrayList<String>) {
        this.cropListData.clear()
        this.cropListData = list
    }

    override fun onCreateViewHolder(parent: ViewGroup, view: Int): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_crop_selection, parent, false)
        return MyViewHolder(itemView)
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var itemViews: View = view

        fun bindItems(
            cropModel: CropModel,
            position: Int,
            listener: AddNewItemListAdapter,
            context: ChemicalAddCropSubActivity,
            categoryData: java.util.ArrayList<String>,
            cropListData: java.util.ArrayList<String>
        ) {
            itemViews.txtAddMore.visibility = View.GONE
            if (position == (itemListData.size - 1)) {
                itemViews.txtAddMore.visibility = View.VISIBLE
            }
            itemViews.txtAddMore.setOnClickListener {
                itemViews.txtAddVolume.clearFocus()
                if (!TextUtils.isEmpty(itemViews.textSubCategory.text) && !TextUtils.isEmpty(
                        itemViews.textCategory.text
                    ) && !TextUtils.isEmpty(itemViews.txtAddVolume.text.toString().trim())
                ) {
                    listener.onAllDataCollect(
                        itemViews.textCategory.text.toString(),
                        itemViews.textSubCategory.text.toString(),
                        itemViews.txtAddVolume.text.toString().trim(),
                        cropModel,
                        position
                    )
                    listener.onAddRow(position)
                } else {
                    context.makeToast("Please fill row data first to add more.")
                }
            }
            itemViews.txtAddVolume.addTextChangedListener(object : TextWatcher {
                override fun beforeTextChanged(
                    s: CharSequence?,
                    start: Int,
                    count: Int,
                    after: Int
                ) {

                }

                override fun onTextChanged(
                    s: CharSequence?,
                    start: Int,
                    before: Int,
                    count: Int
                ) {
                    if (s.toString().isNotEmpty()) {
                        if (!TextUtils.isEmpty(itemViews.textCategory.text.toString()) && !TextUtils.isEmpty(
                                itemViews.textSubCategory.text.toString()
                            )
                        ) {
                            listener.onAllDataCollect(
                                itemViews.textCategory.text.toString(),
                                itemViews.textSubCategory.text.toString(),
                                s.toString().trim(),
                                cropModel,
                                position
                            )
                        }
                    }
                }

                override fun afterTextChanged(s: Editable?) {

                }
            })
            setCategoryDataDropDown(categoryData, itemViews, position)
            setSubCategoryDataDropDown(cropListData, itemViews, position)
        }
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        holder.bindItems(
            itemListData[position],
            position,
            listener,
            context,
            categoryData,
            cropListData
        )
    }

    private fun setSubCategoryDataDropDown(
        cropListData: java.util.ArrayList<String>,
        holder: View,
        position: Int
    ) {
        val selectClass =
            NumberSpinner(context, cropListData, "Search or Select Crop")
        holder.textSubCategory.setOnClickListener {
            if (!selectClass.isEnable) {
                selectClass.showSpinerDialog()
            }
        }
        selectClass.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                holder.textSubCategory.text = item
                holder.txtAddVolume.setText("")
            }
        })
    }

    private fun setCategoryDataDropDown(
        categoryData: ArrayList<String>,
        holder: View,
        pos: Int
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
                holder.textSubCategory.text = ""
                holder.txtAddVolume.setText("")
                listener.onCategorySelected(item, pos)
            }
        })
    }

    override fun getItemCount(): Int = itemListData.size

    interface AddNewItemListAdapter {
        fun onCategorySelected(category: String, position: Int)
        fun onAddRow(position: Int)
        fun onAllDataCollect(
            category: String,
            subCategory: String,
            volume: String,
            cropModel: CropModel,
            position: Int
        )
    }
}