import request from '@/utils/request';
import Resource from '@/api/resource';

class ExportResource extends Resource {
  constructor() {
    super('export-pdf');
  }

  download(query) {
    return request({
      url: '/' + this.uri,
      method: 'get',
      params: query,
    });
  }
}

export { ExportResource as default };
