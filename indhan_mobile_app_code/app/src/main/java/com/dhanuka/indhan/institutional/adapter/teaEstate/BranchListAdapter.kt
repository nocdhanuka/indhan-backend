package com.dhanuka.indhan.institutional.adapter.teaEstate

import android.content.Context
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.teaEstate.TeasEstateListData
import kotlinx.android.synthetic.main.item_branch_view.view.*

class BranchListAdapter(
    private val context: Context,
    private val arrayListOfBranchData: ArrayList<TeasEstateListData>
) : RecyclerView.Adapter<BranchListAdapter.ViewHolder>() {

    private lateinit var clickListener: OnClickViewBranchItem

    fun setViewClickListener(itemClickListener: OnClickViewBranchItem) {
        this.clickListener = itemClickListener
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view ->
                if ((arrayListOfBranchData != null) && (arrayListOfBranchData.size > absoluteAdapterPosition) && (absoluteAdapterPosition != -1)) {
                    clickListener.onClickView(view, arrayListOfBranchData[absoluteAdapterPosition])
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
                .inflate(R.layout.item_branch_view, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.itemView.btnReset.setOnClickListener {
            clickListener.onClickView(it, arrayListOfBranchData[holder.absoluteAdapterPosition])
        }
        holder.itemView.tvBranchName.text = "${arrayListOfBranchData[position].name}"
    }

    override fun getItemCount(): Int = arrayListOfBranchData.size

    interface OnClickViewBranchItem {
        fun onClickView(view: View, subMenu: TeasEstateListData)
    }

}