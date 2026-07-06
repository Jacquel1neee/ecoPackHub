<li>
    <div class="tree-node">
        <span class="level-badge" style="background-color: {{ $node['user']->levelColor }};">
            {{ $node['user']->level }}
        </span>
        <span class="user-name">{{ $node['user']->name }}</span>
        <span class="user-level">{{ $node['user']->levelName }}</span>
        <span class="user-sales">RM {{ number_format($node['user']->group_sales, 2) }}</span>
    </div>
    @if(count($node['children']) > 0)
        <ul>
            @foreach($node['children'] as $child)
                @include('admin.hierarchy.partials.tree-node', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>