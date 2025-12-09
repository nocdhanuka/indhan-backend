package com.dhanuka.indhan.institutional.adapter.teaEstate

import android.content.Context
import android.text.SpannableString
import android.text.style.UnderlineSpan
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.teaEstate.TeasEstateListData
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import kotlinx.android.synthetic.main.tea_estate_item_adapter.view.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext

class TeaEstateListAdapter(
    val context: Context,
    val teaEstateList: List<TeasEstateListData>
) : RecyclerView.Adapter<TeaEstateListAdapter.ViewHolder>() {

    private lateinit var clickListener: OnClickItem

    fun setClickListener(itemClickListener: OnClickItem) {
        this.clickListener = itemClickListener
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                if ((teaEstateList != null) && (teaEstateList.size > absoluteAdapterPosition) && (absoluteAdapterPosition != -1)) {
                    clickListener.onClickView(view, teaEstateList[absoluteAdapterPosition])
                }
            }
        }
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context)
                .inflate(R.layout.tea_estate_item_adapter, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        bindData(holder, position)
    }

    private fun bindData(holder: ViewHolder, position: Int) {
        holder.itemView.tvCompanyName.text = teaEstateList[position].name
        holder.itemView.tvTypeData.text = "Type - ".plus(teaEstateList[position].tea_estate_type_name)
        holder.itemView.tvDistrictData.text = "District - ".plus(teaEstateList[position].district)
        holder.itemView.tvStateData.text = "State - ".plus(teaEstateList[position].state)
        holder.itemView.tvCategoryData.text = "Category - ".plus(teaEstateList[position].company_category)
        if(teaEstateList[position].office_type == "1") {
            holder.itemView.tvOfficeType.text = "Type - Head Office"
            holder.itemView.tvTotalBranch.visibility = View.VISIBLE
            var branchCount = 0
            GlobalScope.launch {
                withContext(Dispatchers.IO) {
                    branchCount = DatabaseClient.getInstance(context).appDatabase.teaEstateListDao()
                        .getBranchCount(teaEstateList[position].tea_estate_id, "2")
                }.run {
                    withContext(Dispatchers.Main) {
                        val content0 =
                            SpannableString("Total Branch : $branchCount")
                        content0.setSpan(UnderlineSpan(), 0, content0.length, 0)
                        holder.itemView.tvTotalBranch.text = content0
                    }
                }
            }
        }else{
            holder.itemView.tvOfficeType.text = "Type - Branch Office"
            holder.itemView.tvTotalBranch.visibility = View.GONE
        }



    }

    override fun getItemCount(): Int = teaEstateList.size

    interface OnClickItem {
        fun onClickView(view: View, subMenu: TeasEstateListData)
    }

}